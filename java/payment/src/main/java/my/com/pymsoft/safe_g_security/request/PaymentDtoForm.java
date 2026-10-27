package my.com.pymsoft.safe_g_security.request;

import java.util.List;

public class PaymentDtoForm {

	private Integer id;
	private Integer totalAmounts;
    private String visitorOrVip;
    private List<PaymentDetailDtoForm> paymentDetailList;
    
	public Integer getId() {
		return id;
	}
	public void setId(Integer id) {
		this.id = id;
	}
	public Integer getTotalAmounts() {
		return totalAmounts;
	}
	public void setTotalAmounts(Integer totalAmounts) {
		this.totalAmounts = totalAmounts;
	}
	public String getVisitorOrVip() {
		return visitorOrVip;
	}
	public void setVisitorOrVip(String visitorOrVip) {
		this.visitorOrVip = visitorOrVip;
	}
	public List<PaymentDetailDtoForm> getPaymentDetailList() {
		return paymentDetailList;
	}
	public void setPaymentDetailList(List<PaymentDetailDtoForm> paymentDetailList) {
		this.paymentDetailList = paymentDetailList;
	}
    
}
