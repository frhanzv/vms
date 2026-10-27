package my.com.pymsoft.safe_g_security.config;

import java.io.InputStream;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.security.KeyFactory;
import java.security.KeyStore;
import java.security.PrivateKey;
import java.security.spec.PKCS8EncodedKeySpec;
import java.util.Base64;

import org.springframework.core.io.ClassPathResource;

public class FpxKeyLoader {
    public static PrivateKey loadPrivateKey(String path) throws Exception {
        // VMS copy: the keystore password comes from the environment, never from the source code
        String pass = System.getenv("FPX_KEYSTORE_PASSWORD");
        if (pass == null || pass.isEmpty()) {
            throw new IllegalStateException("FPX_KEYSTORE_PASSWORD is not set");
        }
        KeyStore keyStore = KeyStore.getInstance("PKCS12");
        InputStream is = new ClassPathResource("merchant.p12").getInputStream();
        keyStore.load(is, pass.toCharArray());

        String alias = keyStore.aliases().nextElement();
        return (PrivateKey) keyStore.getKey(alias, pass.toCharArray());
    	
//    	String key = new String(Files.readAllBytes(Paths.get(path)));
//
//        key = key.replace("-----BEGIN RSA PRIVATE KEY-----", "")
//                 .replace("-----END RSA PRIVATE KEY-----", "")
//                 .replaceAll("\\s", "");
//
//        byte[] decoded = Base64.getDecoder().decode(key);
//
//        PKCS8EncodedKeySpec keySpec = new PKCS8EncodedKeySpec(decoded);
//        KeyFactory keyFactory = KeyFactory.getInstance("RSA");
//
//        return keyFactory.generatePrivate(keySpec);
    }

}
