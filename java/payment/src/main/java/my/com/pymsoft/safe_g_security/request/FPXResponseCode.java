package my.com.pymsoft.safe_g_security.request;

public class FPXResponseCode {

    private String code;
    private String description;

    public FPXResponseCode(String code, String description) {
        this.code = code;
        this.description = description;
    }

    public String getCode() {
        return code;
    }

    public String getDescription() {
        return description;
    }
}
