import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.attribute.PosixFilePermissions;
import java.security.SecureRandom;
import java.util.Base64;
import java.util.Properties;

/** One-time local signing setup. Never prints secrets or replaces an existing key. */
public class CreateReleaseSigning {
    public static void main(String[] args) throws Exception {
        Path root = Path.of("").toAbsolutePath();
        if (!Files.exists(root.resolve("app/build.gradle"))) {
            throw new IllegalStateException("Run from android/absensi with JDK 17.");
        }
        Path signing = root.resolve(".signing");
        Path key = signing.resolve("absensi-release.p12");
        Path config = signing.resolve("release.properties");
        if (Files.exists(key) || Files.exists(config)) {
            throw new IllegalStateException("Signing files already exist; refusing to replace the release identity.");
        }
        Files.createDirectories(signing);
        restrict(signing, "rwx------");
        byte[] bytes = new byte[32];
        new SecureRandom().nextBytes(bytes);
        String password = Base64.getUrlEncoder().withoutPadding().encodeToString(bytes);
        String executable = System.getProperty("os.name").startsWith("Windows") ? "keytool.exe" : "keytool";
        ProcessBuilder builder = new ProcessBuilder(
                Path.of(System.getProperty("java.home"), "bin", executable).toString(),
                "-genkeypair", "-noprompt", "-storetype", "PKCS12",
                "-keystore", key.toString(), "-alias", "absensi",
                "-keyalg", "RSA", "-keysize", "3072", "-validity", "10000",
                "-dname", "CN=Absensi, OU=ICT, O=SMKN 2 Banjarmasin, C=ID",
                "-storepass:env", "ABSENSI_SIGNING_PASSWORD",
                "-keypass:env", "ABSENSI_SIGNING_PASSWORD");
        builder.environment().put("ABSENSI_SIGNING_PASSWORD", password);
        if (builder.inheritIO().start().waitFor() != 0) {
            throw new IllegalStateException("keytool failed; inspect .signing before retrying.");
        }
        restrict(key, "rw-------");
        Properties properties = new Properties();
        properties.setProperty("storeFile", ".signing/absensi-release.p12");
        properties.setProperty("storePassword", password);
        properties.setProperty("keyAlias", "absensi");
        properties.setProperty("keyPassword", password);
        Files.createFile(config);
        restrict(config, "rw-------");
        try (var output = Files.newOutputStream(config)) {
            properties.store(output, "Private local release signing. Back up this directory securely; never commit it.");
        }
        System.out.println("Release signing created in .signing/. Back up BOTH files for future app updates.");
    }

    private static void restrict(Path path, String permissions) throws Exception {
        if (Files.getFileStore(path).supportsFileAttributeView("posix")) {
            Files.setPosixFilePermissions(path, PosixFilePermissions.fromString(permissions));
        }
    }
}
