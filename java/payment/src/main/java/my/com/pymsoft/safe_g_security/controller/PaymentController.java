package my.com.pymsoft.safe_g_security.controller;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.concurrent.TimeUnit;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import my.com.pymsoft.safe_g_security.domain.master.FpxTransaction;
import my.com.pymsoft.safe_g_security.request.FpxPaymentForm;
import my.com.pymsoft.safe_g_security.request.FPXResponseCode;
import my.com.pymsoft.safe_g_security.request.mobile.OpenGateForm;
import my.com.pymsoft.safe_g_security.response.mobile.StatusMo;
import my.com.pymsoft.safe_g_security.service.PayNetFpxService;
import my.com.pymsoft.safe_g_security.service.VendorPassService;

@RestController
@RequestMapping("/api/payment")
public class PaymentController {

	@Autowired
	private VendorPassService vendorPassService;
	
	
	@Autowired
	private PayNetFpxService fpxService;
	

	@PostMapping("/transaction")
	public ResponseEntity<?> bankTransaction(@RequestParam Map<String, String> requestBody) {
        StatusMo status = new StatusMo();

	    System.out.println("Received from Bank "+requestBody);
//	        adamService.doLogin(location);
//	        adamService.triggerGate(gateChannel);
	    
	    FPXResponseCode[] responseCodes = {

			//    new ResponseCode("00", "Approved"),
			    new FPXResponseCode("03", "Invalid Merchant"),
			    new FPXResponseCode("05", "Invalid Seller Or Acquiring Bank Code"),
			    new FPXResponseCode("09", "Transaction Pending"),
			    new FPXResponseCode("12", "Invalid Transaction"),
			    new FPXResponseCode("13", "Invalid Amount"),
			    new FPXResponseCode("14", "Invalid Buyer Account"),
			    new FPXResponseCode("20", "Invalid Response"),
			    new FPXResponseCode("30", "Format Error"),
			    new FPXResponseCode("31", "Invalid Bank"),
			    new FPXResponseCode("39", "No Credit Account"),
			    new FPXResponseCode("45", "Duplicate Seller Order Number"),
			    new FPXResponseCode("46", "Invalid Seller Exchange Or Seller"),
			    new FPXResponseCode("47", "Invalid Currency"),
			    new FPXResponseCode("48", "Maximum Transaction Limit Exceeded"),
			    new FPXResponseCode("49", "Merchant Specific Limit Exceeded"),
			    new FPXResponseCode("50", "Invalid Seller For Merchant Specific Limit"),
			    new FPXResponseCode("51", "Insufficient Funds"),
			    new FPXResponseCode("53", "No Buyer Account Number"),
			    new FPXResponseCode("57", "Transaction Not Permitted"),
			    new FPXResponseCode("58", "Transaction To Merchant Not Permitted"),
			    new FPXResponseCode("70", "Invalid Serial Number"),
			    new FPXResponseCode("76", "Transaction Not Found"),
			    new FPXResponseCode("77", "Invalid Buyer Name Or Buyer ID"),
			    new FPXResponseCode("78", "Decryption Failed"),
			    new FPXResponseCode("79", "Host Decline When Down"),
			    new FPXResponseCode("80", "Buyer Cancel Transaction"),
			    new FPXResponseCode("83", "Invalid Transaction Model"),
			    new FPXResponseCode("84", "Invalid Transaction Type"),
			    new FPXResponseCode("85", "Internal Error At Bank System"),
			    new FPXResponseCode("87", "Debit Failed Exception Handling"),
			    new FPXResponseCode("88", "Credit Failed Exception Handling"),
			    new FPXResponseCode("89", "Transaction Not Received Exception Handling"),
			    new FPXResponseCode("90", "Bank Internet Banking Unavailable"),
			    new FPXResponseCode("92", "Invalid Buyer Bank"),
			    new FPXResponseCode("96", "System Malfunction"),
			    new FPXResponseCode("98", "MAC Error"),
			    new FPXResponseCode("99", "Pending Authorization (Applicable for B2B model)"),
			    new FPXResponseCode("BB", "Blocked Bank"),

			    new FPXResponseCode("BC", "Transaction Cancelled By Customer"),
			    new FPXResponseCode("DA", "Invalid Application Type"),
			    new FPXResponseCode("DB", "Invalid Email Format"),
			    new FPXResponseCode("DC", "Invalid Maximum Frequency"),
			    new FPXResponseCode("DD", "Invalid Frequency Mode"),
			    new FPXResponseCode("DE", "Invalid Expiry Date"),
			    new FPXResponseCode("DF", "Invalid E-Mandate Buyer Bank ID"),
			    new FPXResponseCode("FE", "Internal Error"),
			    new FPXResponseCode("OE", "Transaction Rejected As Not In FPX Operating Hours"),
			    new FPXResponseCode("OF", "Transaction Timeout"),
			    new FPXResponseCode("SB", "Invalid Acquiring Bank Code"),
			    new FPXResponseCode("XA", "Invalid Source IP Address (Applicable for B2B2 model)"),
			    new FPXResponseCode("XB", "Invalid Seller Exchange IP"),
			    new FPXResponseCode("XC", "Seller Exchange Encryption Error"),
			    new FPXResponseCode("XE", "Invalid Message"),
			    new FPXResponseCode("XF", "Invalid Number Of Orders"),
			    new FPXResponseCode("XI", "Invalid Seller Exchange"),
			    new FPXResponseCode("XM", "Invalid FPX Exchange Model"),
			    new FPXResponseCode("XN", "Transaction Rejected Due To Duplicate Seller Exchange Order Number"),
			    new FPXResponseCode("XO", "Duplicate Exchange Order Number"),
			    new FPXResponseCode("XS", "Seller Does Not Belong To Exchange"),
			    new FPXResponseCode("XT", "Invalid Transaction Type"),
			    new FPXResponseCode("XW", "Seller Exchange Date Difference Exceeded"),
			    new FPXResponseCode("1A", "Buyer Session Timeout At Internet Banking Login Page"),
			    new FPXResponseCode("1B", "Buyer Failed To Provide The Necessary Info To Login To Internet Banking Login Page"),
			    new FPXResponseCode("1C", "Buyer Choose Cancel At Login Page"),
			    new FPXResponseCode("1D", "Buyer Session Timeout At Account Selection Page"),
			    new FPXResponseCode("1E", "Buyer Failed To Provide The Necessary Info At Account Selection Page"),
			    new FPXResponseCode("1F", "Buyer Choose Cancel At Account Selection Page"),
			    new FPXResponseCode("1G", "Buyer Session Timeout At TAC Request Page"),
			    new FPXResponseCode("1H", "Buyer Failed To Provide The Necessary Info At TAC Request Page"),
			    new FPXResponseCode("1I", "Buyer Choose Cancel At TAC Request Page"),
			    new FPXResponseCode("1J", "Buyer Session Timeout At Confirmation Page"),
			    new FPXResponseCode("1K", "Buyer Failed To Provide The Necessary Info At Confirmation Page"),
			    new FPXResponseCode("1L", "Buyer Choose Cancel At Confirmation Page"),
			    new FPXResponseCode("1M", "Internet Banking Session Timeout."),
			    new FPXResponseCode("2A", "Transaction Amount Is Lower Than Minimum Limit"),
			    new FPXResponseCode("2X", "Transaction Is Canceled By Merchant"),

			    // Bulk Refund Response Codes
			    new FPXResponseCode("B0", "Order list format error"),
			    new FPXResponseCode("B1", "Invalid seller ID"),
			    new FPXResponseCode("B2", "Seller is not allow to refund"),
			    new FPXResponseCode("B3", "Seller is not allow to do multiple refund"),
			    new FPXResponseCode("B4", "Requested refund amount exceed maximum allowable"),
			    new FPXResponseCode("B5", "Original transaction ID is not found"),
			    new FPXResponseCode("B6", "Original transaction ID status is still pending debit/credit"),
			    new FPXResponseCode("B7", "Original transaction ID status was not successful"),
			    new FPXResponseCode("B8", "Previous refund request still pending debit/credit"),
			    new FPXResponseCode("B9", "Requested refund amount below minimum allowable"),
			    new FPXResponseCode("C1", "Invalid refund transaction model"),
			    new FPXResponseCode("C2", "Invalid refund buyer bank"),
			    new FPXResponseCode("C3", "Invalid refund seller bank"),
			    new FPXResponseCode("C4", "Refund request fail due to no valid order list"),
			    new FPXResponseCode("C5", "Order list contain duplicate seller order number"),
			    new FPXResponseCode("1S", "Bulk Refund Successful Submitted"),
			//    new ResponseCode("00", "Bulk Refund Approved")
			};
	    
	    FpxTransaction fpxtran= mapToFpxTransaction(requestBody);
	    fpxService.saveFpxTransaction(fpxtran);
	      
	    String receiptNo = (String) requestBody.get("fpx_sellerOrderNo");
	    Date fpxPaymentDate = new Date();
	    boolean exists = Arrays.stream(responseCodes)
	            .anyMatch(rc -> rc.getCode().equalsIgnoreCase(fpxtran.getFpxDebitAuthCode()));
	    System.out.println("DebitAuthCode = >>>> "+ fpxtran.getFpxDebitAuthCode());
	    System.out.println("DebitAuthCodeCheck = >>>> "+ exists);
	    
	    if(!exists) {
	    	 vendorPassService.updatePaymentStatus(receiptNo, fpxPaymentDate);
	    }
	    FpxPaymentForm fpx=new FpxPaymentForm();
	    fpx.setReceiptNo(receiptNo);
	   // fpxService.sendAE(fpx);
	  
//        return ResponseEntity
//        .status(HttpStatus.OK)   // 302 redirect
//        .header("Location", "http://34.44.153.214:8080/#/payment-result?receipt=" 
//                 + receiptNo + "&status=" + status.getStatus())
//        .build();
//	    return ResponseEntity.ok()
//	    	    .contentType(MediaType.TEXT_HTML)
//	    	    .body("<html><body>" +
//	    	          "<script>" +
//	    	          "window.location.href='http://34.44.153.214:8080/#/payment-result?receipt="
//	    	          + receiptNo + "&status="+status.getStatus()+"';" +
//	    	          "</script>" +
//	    	          "</body></html>");
	   // return ResponseEntity.ok().build();
	    return ResponseEntity.ok("OK");

    }
	
	public FpxTransaction mapToFpxTransaction(Map<String, String> map) {
	    FpxTransaction txn = new FpxTransaction();

	    txn.setFpxCreditAuthNo(map.get("fpx_creditAuthNo"));
	    txn.setFpxMsgToken(map.get("fpx_msgToken"));
	    txn.setFpxTxnCurrency(map.get("fpx_txnCurrency"));
	    txn.setFpxSellerOrderNo(map.get("fpx_sellerOrderNo"));
	    txn.setFpxCreditAuthCode(map.get("fpx_creditAuthCode"));
	    txn.setFpxTxnTime(map.get("fpx_fpxTxnTime"));
	    txn.setFpxMakerName(map.get("fpx_makerName"));
	    txn.setFpxDebitAuthNo(map.get("fpx_debitAuthNo"));
	    txn.setFpxTxnAmount(map.get("fpx_txnAmount"));
	    txn.setFpxSellerExId(map.get("fpx_sellerExId"));
	    txn.setFpxBuyerBankId(map.get("fpx_buyerBankId"));
	    txn.setFpxMsgType(map.get("fpx_msgType"));
	    txn.setFpxCheckSum(map.get("fpx_checkSum"));
	    txn.setFpxSellerExOrderNo(map.get("fpx_sellerExOrderNo"));
	    txn.setFpxBuyerName(map.get("fpx_buyerName"));
	    txn.setFpxBuyerIban(map.get("fpx_buyerIban"));
	    txn.setFpxSellerId(map.get("fpx_sellerId"));
	    txn.setFpxSellerTxnTime(map.get("fpx_sellerTxnTime"));
	    txn.setFpxBuyerId(map.get("fpx_buyerId"));
	    txn.setFpxDebitAuthCode(map.get("fpx_debitAuthCode"));
	    txn.setFpxBuyerBankBranch(map.get("fpx_buyerBankBranch"));
	    txn.setFpxFpxTxnId(map.get("fpx_fpxTxnId"));

	    return txn;
	}
	
	@PostMapping("/result-page")
    public ResponseEntity<?> getResulPage(@RequestParam Map<String, String> requestBody) {
		  String receiptNo = (String) requestBody.get("fpx_sellerOrderNo");
	    return ResponseEntity.ok()
	    .contentType(MediaType.TEXT_HTML)
	    .body("<html><body>" +
	          "<script>" +
	          "window.location.href='https://staging.pbksb-acms.com.my/#/payment-result?sellerOrderNo="
	          + receiptNo + "';" +
	          "</script>" +
	          "</body></html>");
    }
	

}
