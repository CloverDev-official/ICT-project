package id.sch.smkn2.absensi;

/** Run with javac/java; no Android device or third-party test dependency required. */
public final class ServerAddressTest {
    public static void main(String[] args) {
        ServerAddress root = new ServerAddress(" https://school.example/ ");
        equal("https://school.example/mpanel/scan-qrcode", root.scanUrl());
        equal(root.scanUrl(), new ServerAddress(root.scanUrl()).scanUrl());
        equal(root.scanUrl(), new ServerAddress(root.scanUrl() + "/").scanUrl());
        equal("https://school.example/app/mpanel/scan-qrcode",
                new ServerAddress("https://school.example/app/").scanUrl());
        equal("https://school.example/app/mpanel/scan-qrcode",
                new ServerAddress("https://school.example/app/mpanel/scan-qrcode").scanUrl());
        check(root.trusts("https://school.example/"));
        check(root.trusts("https://school.example:443/mpanel/scan-qrcode"));
        for (String untrusted : new String[]{"http://school.example", "https://evil.example",
                "https://school.example.evil.example", "https://school.example:444/",
                "https://user@school.example", "file:///etc/passwd", "javascript:alert(1)", "not a url"}) {
            check(!root.trusts(untrusted));
        }
        for (String invalid : new String[]{"http://school.example", "https://user:pass@school.example",
                "https://school.example/?token=secret", "https://school.example/#fragment", "https://", ""}) {
            try {
                new ServerAddress(invalid);
                throw new AssertionError("Accepted invalid address: " + invalid);
            } catch (IllegalArgumentException expected) { }
        }
        System.out.println("ServerAddress: route, subdirectory, HTTPS, and origin validation passed.");
    }

    private static void equal(String expected, String actual) {
        if (!expected.equals(actual)) throw new AssertionError(expected + " != " + actual);
    }

    private static void check(boolean value) {
        if (!value) throw new AssertionError();
    }
}
