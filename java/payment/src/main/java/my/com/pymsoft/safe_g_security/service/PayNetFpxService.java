package my.com.pymsoft.safe_g_security.service;

import java.util.List;
import java.util.Map;

import org.springframework.stereotype.Service;

import my.com.pymsoft.safe_g_security.domain.master.FpxTransaction;
import my.com.pymsoft.safe_g_security.request.FpxPaymentForm;
import my.com.pymsoft.safe_g_security.response.FpxBank;

@Service
public interface PayNetFpxService {

	Map<String, String> retrieveBankList(String type);

	String sendAuthorizationRequest(FpxPaymentForm form);

	String sendAE(FpxPaymentForm fpx);

	void saveFpxTransaction(FpxTransaction fpxtran);

	FpxTransaction getFpxTransactionBySellerNo(String sellerOrderNo);

}
