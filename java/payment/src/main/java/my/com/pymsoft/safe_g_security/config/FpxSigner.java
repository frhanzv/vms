package my.com.pymsoft.safe_g_security.config;

import java.nio.charset.StandardCharsets;
import java.security.PrivateKey;
import java.security.Signature;
import java.util.Base64;

public class FpxSigner {
    public static String sign(String data, PrivateKey privateKey) throws Exception {
//        Signature signature = Signature.getInstance("SHA1withRSA");
//        signature.initSign(privateKey);
//        signature.update(data.getBytes(StandardCharsets.UTF_8));
//        return Base64.getEncoder().encodeToString(signature.sign());
        
        Signature signature = Signature.getInstance("SHA1withRSA");
        signature.initSign(privateKey);
        signature.update(data.getBytes("UTF-8"));

        byte[] signedBytes = signature.sign();

        return Base64.getEncoder().encodeToString(signedBytes);
    }

}
