package my.com.pymsoft.safe_g_security.response.mobile;

import java.io.Serializable;

public class StatusMo implements Serializable {

    /**
     *
     */
    private static final long serialVersionUID = 1L;

    public static final String SUCCESS  = "SUCCESS";
    public static final String FAIL     = "fail";

    private String     status = SUCCESS;
    private String     errorMessage = "";
    private String     printSlip = "0";

    public String getPrintSlip() {
        return printSlip;
    }

    public void setPrintSlip(String printSlip) {
        this.printSlip = printSlip;
    }

    public StatusMo() {
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getErrorMessage() {
        return errorMessage;
    }

    public void setErrorMessage(String errorMessage) {
        this.errorMessage = errorMessage;
    }

}
