package my.com.pymsoft.safe_g_security.request;

import lombok.Getter;
import lombok.Setter;

@Getter
@Setter
public class FpxPaymentForm {
	private String orderId;
	private String bankId;
	private String amount;
	private String currency;
	private String email;
	private String name;
	private String buyerId;
	private String receiptNo;
	private String sellerExOrderNo;
	private String version;
	private String sellerBankCode;
	private String productDesc;
	private String buyerBankBranch;
	private String buyerAccNo;
	private String makerName;
	private String paymentMode;
	
}
