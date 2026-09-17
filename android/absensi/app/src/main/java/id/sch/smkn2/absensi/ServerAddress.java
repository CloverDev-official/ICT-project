package id.sch.smkn2.absensi;

import java.net.URI;
import java.net.URISyntaxException;

/** Keeps WebView navigation and camera access on the configured HTTPS origin. */
final class ServerAddress {
    static final String SCAN_PATH = "/mpanel/scan-qrcode";
    private final URI base;

    ServerAddress(String input) {
        try {
            URI parsed = new URI(input.trim());
            if (!"https".equalsIgnoreCase(parsed.getScheme()) || parsed.getHost() == null
                    || parsed.getRawUserInfo() != null || parsed.getRawQuery() != null
                    || parsed.getRawFragment() != null || parsed.getPort() < -1
                    || parsed.getPort() == 0 || parsed.getPort() > 65535) {
                throw new IllegalArgumentException("Gunakan alamat HTTPS tanpa query atau akun di URL.");
            }
            String path = parsed.normalize().getRawPath();
            while (path.endsWith("/")) path = path.substring(0, path.length() - 1);
            if (path.endsWith(SCAN_PATH)) path = path.substring(0, path.length() - SCAN_PATH.length());
            base = new URI("https://" + parsed.getRawAuthority() + path);
        } catch (URISyntaxException error) {
            throw new IllegalArgumentException("Alamat server tidak valid.", error);
        }
    }

    String baseUrl() {
        return base.toASCIIString();
    }

    String scanUrl() {
        return baseUrl() + SCAN_PATH;
    }

    boolean trusts(String url) {
        if (url == null) return false;
        try {
            URI candidate = new URI(url);
            return "https".equalsIgnoreCase(candidate.getScheme())
                    && candidate.getRawUserInfo() == null
                    && base.getHost().equalsIgnoreCase(candidate.getHost())
                    && port(base) == port(candidate);
        } catch (URISyntaxException error) {
            return false;
        }
    }

    private static int port(URI uri) {
        return uri.getPort() == -1 ? 443 : uri.getPort();
    }
}
