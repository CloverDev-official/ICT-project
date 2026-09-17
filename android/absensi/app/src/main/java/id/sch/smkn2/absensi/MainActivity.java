package id.sch.smkn2.absensi;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.graphics.Color;
import android.net.http.SslError;
import android.os.Build;
import android.os.Bundle;
import android.text.InputType;
import android.view.Gravity;
import android.view.View;
import android.view.WindowInsets;
import android.view.WindowInsetsController;
import android.view.WindowManager;
import android.webkit.CookieManager;
import android.webkit.PermissionRequest;
import android.webkit.SslErrorHandler;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebResourceResponse;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import java.util.Arrays;

public final class MainActivity extends Activity {
    private static final int CAMERA_REQUEST = 10;
    private WebView webView;
    private FrameLayout root;
    private LinearLayout errorPanel;
    private TextView errorText;
    private SharedPreferences preferences;
    private ServerAddress server;
    private PermissionRequest cameraRequest;
    private boolean askingCamera;
    private boolean paused;

    @Override
    public void onCreate(Bundle state) {
        super.onCreate(state);
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
        preferences = getSharedPreferences("absensi", MODE_PRIVATE);
        createContent();
        enterFullscreen();
        String saved = preferences.getString("server", "");
        if (saved.isEmpty()) {
            configureServer();
        } else {
            try {
                server = new ServerAddress(saved);
                webView.loadUrl(server.scanUrl());
            } catch (IllegalArgumentException error) {
                configureServer();
            }
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    private void createContent() {
        root = new FrameLayout(this);
        root.setBackgroundColor(Color.rgb(2, 6, 23));
        webView = new WebView(this);
        root.addView(webView, new FrameLayout.LayoutParams(-1, -1));
        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setMediaPlaybackRequiresUserGesture(false);
        settings.setAllowFileAccess(false);
        settings.setAllowContentAccess(false);
        settings.setMixedContentMode(WebSettings.MIXED_CONTENT_NEVER_ALLOW);
        settings.setUserAgentString(settings.getUserAgentString() + " AbsensiAndroid/1");
        CookieManager.getInstance().setAcceptCookie(true);
        CookieManager.getInstance().setAcceptThirdPartyCookies(webView, false);
        webView.setWebViewClient(new ScannerClient());
        webView.setWebChromeClient(new WebChromeClient() {
            @Override
            public void onPermissionRequest(PermissionRequest request) {
                runOnUiThread(() -> requestCamera(request));
            }

            @Override
            public void onPermissionRequestCanceled(PermissionRequest request) {
                if (cameraRequest == request) cameraRequest = null;
            }
        });

        errorPanel = new LinearLayout(this);
        errorPanel.setOrientation(LinearLayout.VERTICAL);
        errorPanel.setGravity(Gravity.CENTER);
        errorPanel.setPadding(dp(24), dp(24), dp(24), dp(24));
        errorPanel.setBackgroundColor(Color.rgb(2, 6, 23));
        errorText = new TextView(this);
        errorText.setTextColor(Color.WHITE);
        errorText.setTextSize(18);
        errorText.setGravity(Gravity.CENTER);
        errorPanel.addView(errorText);
        Button retry = new Button(this);
        retry.setText(R.string.retry);
        retry.setOnClickListener(view -> openScanner());
        errorPanel.addView(retry);
        errorPanel.setVisibility(View.GONE);
        root.addView(errorPanel, new FrameLayout.LayoutParams(-1, -1));

        Button menu = new Button(this);
        menu.setText("⋮");
        menu.setTextSize(24);
        menu.setContentDescription("Menu aplikasi Absensi");
        menu.setOnClickListener(view -> showMenu());
        FrameLayout.LayoutParams menuParams = new FrameLayout.LayoutParams(dp(48), dp(48), Gravity.TOP | Gravity.END);
        root.addView(menu, menuParams);
        setContentView(root);

        if (Build.VERSION.SDK_INT >= 33) {
            getOnBackInvokedDispatcher().registerOnBackInvokedCallback(
                    android.window.OnBackInvokedDispatcher.PRIORITY_DEFAULT, this::showMenu);
        }

        if (Build.VERSION.SDK_INT >= 30) {
            getWindow().setDecorFitsSystemWindows(false);
            root.setOnApplyWindowInsetsListener((view, insets) -> {
                android.graphics.Insets safe = insets.getInsets(WindowInsets.Type.displayCutout());
                int keyboard = insets.getInsets(WindowInsets.Type.ime()).bottom;
                view.setPadding(safe.left, safe.top, safe.right, Math.max(safe.bottom, keyboard));
                return insets;
            });
        }
    }

    private void enterFullscreen() {
        if (Build.VERSION.SDK_INT >= 30) {
            WindowInsetsController controller = getWindow().getInsetsController();
            if (controller != null) {
                controller.setSystemBarsBehavior(WindowInsetsController.BEHAVIOR_SHOW_TRANSIENT_BARS_BY_SWIPE);
                controller.hide(WindowInsets.Type.systemBars());
            }
        } else {
            getWindow().getDecorView().setSystemUiVisibility(
                    View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY | View.SYSTEM_UI_FLAG_FULLSCREEN
                            | View.SYSTEM_UI_FLAG_HIDE_NAVIGATION | View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                            | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);
        }
    }

    @Override
    public void onWindowFocusChanged(boolean focused) {
        super.onWindowFocusChanged(focused);
        if (focused) enterFullscreen();
    }

    @Override
    protected void onResume() {
        super.onResume();
        paused = false;
        if (webView != null) webView.onResume();
        enterFullscreen();
    }

    @Override
    protected void onPause() {
        paused = true;
        // The Android permission dialog can pause the requesting activity.
        if (!askingCamera) cancelCamera();
        webView.onPause();
        CookieManager.getInstance().flush();
        super.onPause();
    }

    @Override
    protected void onDestroy() {
        cancelCamera();
        root.removeView(webView);
        webView.destroy();
        super.onDestroy();
    }

    @Override
    public void onBackPressed() {
        showMenu();
    }

    private void showMenu() {
        new AlertDialog.Builder(this).setTitle("Absensi")
                .setItems(new String[]{"Scan QR", "Muat ulang", "Kembali ke halaman sebelumnya", "Alamat server", "Keluar aplikasi"},
                        (dialog, item) -> {
                            switch (item) {
                                case 0: openScanner(); break;
                                case 1:
                                    if (server == null) configureServer();
                                    else if (errorPanel.getVisibility() == View.VISIBLE) openScanner();
                                    else webView.reload();
                                    break;
                                case 2: if (webView.canGoBack()) webView.goBack(); break;
                                case 3: configureServer(); break;
                                case 4: finish(); break;
                                default: break;
                            }
                        }).show();
    }

    private void openScanner() {
        if (server == null) configureServer();
        else webView.loadUrl(server.scanUrl());
    }

    private void configureServer() {
        EditText address = new EditText(this);
        address.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_VARIATION_URI);
        address.setSingleLine(true);
        address.setHint("https://absensi.sekolah.sch.id");
        address.setText(server == null ? "" : server.baseUrl());
        AlertDialog dialog = new AlertDialog.Builder(this)
                .setTitle("Alamat server Absensi")
                .setMessage("Masukkan alamat HTTPS website sekolah. Halaman Scan QR akan dibuka otomatis.")
                .setView(address)
                .setPositiveButton("Simpan", null)
                .setNegativeButton("Batal", (ignored, which) -> {
                    if (server == null) finish();
                })
                .setCancelable(server != null)
                .create();
        dialog.setOnShowListener(ignored -> dialog.getButton(AlertDialog.BUTTON_POSITIVE).setOnClickListener(view -> {
            try {
                ServerAddress next = new ServerAddress(address.getText().toString());
                boolean changed = server != null && !next.baseUrl().equals(server.baseUrl());
                cancelCamera();
                server = next;
                preferences.edit().putString("server", server.baseUrl()).apply();
                dialog.dismiss();
                if (changed) {
                    webView.stopLoading();
                    webView.loadUrl("about:blank");
                    CookieManager.getInstance().removeAllCookies(removed -> {
                        CookieManager.getInstance().flush();
                        webView.clearHistory();
                        openScanner();
                    });
                } else openScanner();
            } catch (IllegalArgumentException error) {
                address.setError(error.getMessage());
            }
        }));
        dialog.show();
    }

    private void requestCamera(PermissionRequest request) {
        if (paused || server == null || !server.trusts(request.getOrigin().toString())
                || !server.trusts(webView.getUrl())
                || !Arrays.asList(request.getResources()).contains(PermissionRequest.RESOURCE_VIDEO_CAPTURE)) {
            request.deny();
            return;
        }
        cancelCamera();
        cameraRequest = request;
        if (checkSelfPermission(Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED) {
            grantCamera();
        } else if (!askingCamera) {
            askingCamera = true;
            requestPermissions(new String[]{Manifest.permission.CAMERA}, CAMERA_REQUEST);
        }
    }

    private void grantCamera() {
        PermissionRequest request = cameraRequest;
        cameraRequest = null;
        if (request == null) return;
        if (server != null && server.trusts(request.getOrigin().toString()) && server.trusts(webView.getUrl())) {
            // Do not grant microphone or future WebView resources implicitly.
            request.grant(new String[]{PermissionRequest.RESOURCE_VIDEO_CAPTURE});
        } else request.deny();
    }

    private void cancelCamera() {
        if (cameraRequest != null) {
            cameraRequest.deny();
            cameraRequest = null;
        }
    }

    @Override
    public void onRequestPermissionsResult(int code, String[] permissions, int[] results) {
        super.onRequestPermissionsResult(code, permissions, results);
        if (code != CAMERA_REQUEST) return;
        askingCamera = false;
        if (results.length > 0 && results[0] == PackageManager.PERMISSION_GRANTED) {
            if (cameraRequest != null) grantCamera();
            else if (server != null) webView.reload();
        } else {
            cancelCamera();
            Toast.makeText(this, "Kamera belum diizinkan. Aktifkan izin Kamera di pengaturan aplikasi, lalu muat ulang.", Toast.LENGTH_LONG).show();
        }
        enterFullscreen();
    }

    private void showError(String message) {
        cancelCamera();
        errorText.setText(message);
        errorPanel.setVisibility(View.VISIBLE);
    }

    private int dp(int value) {
        return Math.round(value * getResources().getDisplayMetrics().density);
    }

    private final class ScannerClient extends WebViewClient {
        @Override
        public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
            if (server != null && server.trusts(request.getUrl().toString())) return false;
            Toast.makeText(MainActivity.this, "Tautan di luar server Absensi tidak dibuka di aplikasi.", Toast.LENGTH_SHORT).show();
            return true;
        }

        @Override
        public void onPageStarted(WebView view, String url, android.graphics.Bitmap icon) {
            cancelCamera();
            if (!"about:blank".equals(url) && (server == null || !server.trusts(url))) {
                view.stopLoading();
                showError("Alamat tujuan berbeda dari server Absensi. Periksa alamat server.");
                return;
            }
            errorPanel.setVisibility(View.GONE);
            enterFullscreen();
        }

        @Override
        public void onPageFinished(WebView view, String url) {
            CookieManager.getInstance().flush();
            enterFullscreen();
        }

        @Override
        public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
            if (request.isForMainFrame()) showError("Server tidak dapat diakses. Periksa koneksi dan alamat server, lalu coba lagi.");
        }

        @Override
        public void onReceivedHttpError(WebView view, WebResourceRequest request, WebResourceResponse response) {
            if (request.isForMainFrame() && response.getStatusCode() >= 500) {
                showError("Server sedang bermasalah. Silakan coba lagi.");
            }
        }

        @Override
        public void onReceivedSslError(WebView view, SslErrorHandler handler, SslError error) {
            handler.cancel();
            showError("Sertifikat HTTPS tidak valid. Hubungi pengelola server untuk memperbaikinya.");
        }
    }
}
