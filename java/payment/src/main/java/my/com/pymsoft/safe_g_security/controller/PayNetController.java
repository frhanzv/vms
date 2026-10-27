package my.com.pymsoft.safe_g_security.controller;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.List;
import java.util.Map;    
import java.util.UUID;
import org.springframework.http.HttpHeaders;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.context.ApplicationContext;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseBody;
import org.springframework.web.bind.annotation.RestController;

import my.com.pymsoft.safe_g_security.request.FPXResponseCode;
import my.com.pymsoft.safe_g_security.request.FpxPaymentForm;
import my.com.pymsoft.safe_g_security.service.PayNetFpxService;
import my.com.pymsoft.safe_g_security.domain.master.FpxTransaction;
import my.com.pymsoft.safe_g_security.repo.master.FpxBankRepo;
import my.com.pymsoft.safe_g_security.repo.master.PaymentHeaderRepo;

@RestController
@RequestMapping("/api/fpx")
public class PayNetController {
    
    @Autowired
    private PayNetFpxService fpxService;

    @Autowired
    private ApplicationContext applicationContext;
    
    @Autowired
    private FpxBankRepo fpxBankRepo;
    
    @Value("${staging}")
	private String staging;
    
    @GetMapping("/banks")
    public ResponseEntity<?> getBanks(@RequestParam("type") String type) {
        System.out.println("=== /banks endpoint hit ==="); 
        try {
            Map<String, String> banks = fpxService.retrieveBankList(type);
            List<Map<String, String>> bankLists = new ArrayList<>();
            if (banks.containsKey("fpx_bankList")) {
                String bankList = banks.get("fpx_bankList");
                String[] bankEntries = bankList.split(",");
                
               String reqType="";
               if(staging.equals("true")) {
            	   if(type.equalsIgnoreCase("B2C")) {
                	   reqType="Staging B2C";
                   }else {
                	   reqType="Staging B2B";
                   }
               }else {
            	   if(type.equalsIgnoreCase("B2C")) {
                	   reqType="Production B2C";
                   }else {
                	   reqType="Production B2B";
                   }
               }
             
               // formattedBanks.append("Available Banks:\n");
                for (String entry : bankEntries) {
                    String[] parts = entry.split("~");
                    StringBuilder formattedBanks = new StringBuilder();
                    String bankId="";
                    Map<String, String> bank =new HashMap<>();
                    if (parts.length == 2) {
                    	bankId = parts[0];
                    	System.out.println("BankName===> "+fpxBankRepo.findByBankId(parts[0],reqType));
                    	if(fpxBankRepo.findByBankId(parts[0],reqType)!= null) {
                    		  formattedBanks.append(fpxBankRepo.findByBankId(parts[0],reqType))
                              .append(parts[1].equals("A") ? "" : " (Offline)");
                    	}
                    }
                    bank.put("bankId",bankId);
                    bank.put("displayName",formattedBanks.toString());
                    bankLists.add(bank);
                }
                return ResponseEntity.ok(bankLists);
            }
            return ResponseEntity.ok(banks);
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Error: " + e.getMessage());
        }
    }
    
    @GetMapping("/bank-list")
    public ResponseEntity<?> getFormattedBankList() {
        try {
            Map<String, String> banks = fpxService.retrieveBankList("B2C");
            if (banks.containsKey("fpx_bankList")) {
                String bankList = banks.get("fpx_bankList");
                String[] bankEntries = bankList.split(",");
                
                StringBuilder formattedBanks = new StringBuilder();
                formattedBanks.append("Available Banks:\n");
                for (String entry : bankEntries) {
                    String[] parts = entry.split("~");
                    if (parts.length == 2) {
                        formattedBanks.append("Bank Code: ").append(fpxBankRepo.findByBankId(parts[0],"Staging B2C"))
                                    .append(" - Status: ").append(parts[1].equals("A") ? "Online" : "Offline")
                                    .append("\n");
                    }
                }
                return ResponseEntity.ok(formattedBanks.toString());
            }
            return ResponseEntity.ok(banks);
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Error: " + e.getMessage());
        }
    }

@PostMapping(value = "/authorize", consumes = MediaType.APPLICATION_JSON_VALUE, produces = MediaType.TEXT_HTML_VALUE)
@ResponseBody
public ResponseEntity<String> authorize(@RequestBody Map<String, Object> requestMap) {
    try {
        FpxPaymentForm form = new FpxPaymentForm();
        // Handle bank selection
        if (requestMap.containsKey("bankId")) {
            form.setBankId((String) requestMap.get("bankId"));
        } else if (requestMap.containsKey("bank")) {
            form.setBankId((String) requestMap.get("bank"));
        } else {
            return ResponseEntity.badRequest().body("Missing required field: bankId or bank");
        }
        
        // Set email and name
        form.setEmail((String) requestMap.get("email"));
        form.setName((String) requestMap.get("name"));
        form.setPaymentMode((String) requestMap.get("paymentMode"));
        
        // 🔥 CRITICAL: Receipt number from request
        String receiptNo = (String) requestMap.get("receiptNo");
        form.setReceiptNo(receiptNo);        
        
        // Generate sellerExOrderNo
        String sellerExOrderNo = "EX" + UUID.randomUUID().toString().replace("-", "").substring(0, 16);
        form.setSellerExOrderNo(sellerExOrderNo);
        
        // Static Values
        form.setAmount(requestMap.get("amount").toString());
        form.setCurrency("MYR");
        form.setVersion("6.0");
        form.setSellerBankCode("01");
        
        System.out.println("=== AUTHORIZE API CALLED ===");
        System.out.println("receiptNo: " + receiptNo); // Should be "00063"
        
        String response = fpxService.sendAuthorizationRequest(form);
         return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_TYPE, MediaType.TEXT_HTML_VALUE)
                .body(response);
        
    } catch (Exception e) {
        e.printStackTrace();
        return ResponseEntity.badRequest().body("ERROR: " + e.getMessage());
    }
}

// 🔥 Helper method to get next receipt number from database
private String getNextReceiptNumber() {
    try {
        // Get PaymentHeaderRepo from Spring context
        PaymentHeaderRepo paymentHeaderRepo = applicationContext.getBean(PaymentHeaderRepo.class);
        
        String maxReceipt = paymentHeaderRepo.findMaxReceiptNo();
        System.out.println("Current max receipt in DB: " + maxReceipt);
        
        int currentMax = 0;
        if (maxReceipt != null && !maxReceipt.isEmpty()) {
            currentMax = Integer.parseInt(maxReceipt);
        }
        
        int nextNum = currentMax + 1;
        String nextReceipt = String.format("%05d", nextNum); // "00063"
        
        System.out.println("Next receipt number: " + nextReceipt);
        return nextReceipt;
        
    } catch (Exception e) {
        System.out.println("Error generating receipt: " + e.getMessage());
        e.printStackTrace();
        return "00001"; // Default fallback
    }
}
    
    // ❌ YEH POORA METHOD HATA DO - iski wajah se error aa raha hai
    /*
    private String getNextReceiptNumber() {
        try {
            PaymentHeaderRepo paymentHeaderRepo = applicationContext.getBean(PaymentHeaderRepo.class);
            String maxReceipt = paymentHeaderRepo.findMaxReceiptNo();
            int currentMax = (maxReceipt != null && !maxReceipt.isEmpty()) ? 
                             Integer.parseInt(maxReceipt) : 0;
            int nextNum = currentMax + 1;
            return String.format("%05d", nextNum);
        } catch (Exception e) {
            e.printStackTrace();
            return "00001";
        }
    }
    */
    
    // Test endpoint to see what parameters are being sent
    @PostMapping(value = "/debug-authorize", consumes = MediaType.APPLICATION_JSON_VALUE)
    public ResponseEntity<?> debugAuthorize(@RequestBody FpxPaymentForm form) {
        try {
            // Set defaults
            if (form.getCurrency() == null) form.setCurrency("MYR");
            if (form.getVersion() == null) form.setVersion("6.0");
            if (form.getSellerBankCode() == null) form.setSellerBankCode(form.getBankId());
            
            // Log all fields
            Map<String, String> debug = new HashMap<>();
            debug.put("receiptNo", form.getReceiptNo()); 
            debug.put("bankId", form.getBankId());
            debug.put("sellerBankCode", form.getSellerBankCode());
            debug.put("amount", form.getAmount());
            debug.put("currency", form.getCurrency());
            debug.put("email", form.getEmail());
            debug.put("name", form.getName());
            debug.put("buyerId", form.getBuyerId());
            debug.put("sellerExOrderNo", form.getSellerExOrderNo());
            debug.put("productDesc", form.getProductDesc());
            debug.put("version", form.getVersion());
            debug.put("buyerBankBranch", form.getBuyerBankBranch());
            debug.put("buyerAccNo", form.getBuyerAccNo());
            debug.put("makerName", form.getMakerName());
            
            return ResponseEntity.ok(debug);
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Error: " + e.getMessage());
        }
    }

    @GetMapping("/banks/formatted")
    public ResponseEntity<?> getFormattedBanks() {
        try {
            Map<String, String> banks = fpxService.retrieveBankList("B2C");
            
            if (banks.containsKey("fpx_bankList")) {
                String bankList = banks.get("fpx_bankList");
                String[] bankEntries = bankList.split(",");
                
                List<Map<String, String>> formattedBanks = new ArrayList<>();
                
                for (String entry : bankEntries) {
                    String[] parts = entry.split("~");
                    if (parts.length == 2) {
                        Map<String, String> bank = new HashMap<>();
                        bank.put("bankCode", parts[0]);
                        bank.put("status", parts[1].equals("A") ? "ONLINE" : "OFFLINE");
                        bank.put("statusCode", parts[1]);
                        formattedBanks.add(bank);
                    }
                }
                
                Map<String, Object> response = new HashMap<>();
                response.put("status", "success");
                response.put("banks", formattedBanks);
                response.put("total", formattedBanks.size());
                
                return ResponseEntity.ok(response);
            }
            
            return ResponseEntity.ok(banks);
            
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Error: " + e.getMessage());
        }
    }
    
	@GetMapping("/result")
    public ResponseEntity<?> getResulPage(@RequestParam("sellerOrderNo") String sellerOrderNo) {
		FpxTransaction fpxtran=fpxService.getFpxTransactionBySellerNo(sellerOrderNo);
		 Map<String, Object> response = new HashMap<>();
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
		 if(fpxtran!=null) {
			 response.put("date", fpxtran.getFpxTxnTime());
	         response.put("payTo", "SafeG");
	         response.put("bank", fpxtran.getFpxBuyerBankBranch());
	         response.put("amount", fpxtran.getFpxTxnAmount());
	         response.put("referenceNo", sellerOrderNo);
	         response.put("fpxTransactionId", fpxtran.getFpxFpxTxnId());
	         boolean exists = Arrays.stream(responseCodes)
	 	            .anyMatch(rc -> rc.getCode().equalsIgnoreCase(fpxtran.getFpxDebitAuthCode()));
	         if(exists) {
//	        	  response.put("status", "FAIL");
	         }else {
	        	  response.put("status", "SUCCESS");
	         }
	       
		 }
         return ResponseEntity.ok(response);
	
    }
}


