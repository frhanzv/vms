package my.com.pymsoft.safe_g_security.domain.master;

import java.util.Date;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.FetchType;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.ManyToOne;
import javax.persistence.SequenceGenerator;
import javax.persistence.Table;

import com.fasterxml.jackson.annotation.JsonView;

import my.com.pymsoft.safe_g_security.domain.master.master.BaseWithDateDomain;
import my.com.pymsoft.safe_g_security.domain.master.master.Status;
import my.com.pymsoft.safe_g_security.view.View;

@Entity
@Table(name = "SAFEG_PAYMENT_HEADER")
public class PaymentHeader extends BaseWithDateDomain {

    @JsonView(View.PaymentHeader.class)
    @Column(name="ID",columnDefinition="NUMBER")
    @Id
    @SequenceGenerator(initialValue=1, sequenceName="PAYMENT_HEADER_SEQ",allocationSize=1,name="payment_header_seq")
    @GeneratedValue(generator="payment_header_seq",strategy=GenerationType.SEQUENCE)
    private Integer id;

    @JsonView(View.PaymentHeader.class)
    @Column(name = "TYPE", nullable = true, length = 255)
    private String type;
    
    @JsonView(View.PaymentHeader.class)
    @Column(name = "TOTALAMOUNT",columnDefinition="NUMBER")
    private Integer totalAmount;   
    
    @JsonView(View.PaymentHeader.class)
    @Column(name = "RECEIPTNO", nullable = true, length = 255)
    private String receiptNo;
    
    @JsonView(View.PaymentHeader.class)
    @Column(name = "VPID",columnDefinition="NUMBER")
    private Integer vpId;

    @JsonView(View.PaymentHeader.class)
    @JoinColumn(name="STATUSID")
    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    private Status status;
    
    @Column(name = "FPX_PAYMENT_DATE", nullable = false,columnDefinition="TIMESTAMP")
    private Date fpxPaymentDate;
    
    @Column(name = "PAYMENT_STATUS")
    private String paymentStatus;

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getType() {
		return type;
	}

	public void setType(String type) {
		this.type = type;
	}

	public Integer getTotalAmount() {
		return totalAmount;
	}

	public void setTotalAmount(Integer totalAmount) {
		this.totalAmount = totalAmount;
	}

	public String getReceiptNo() {
		return receiptNo;
	}

	public void setReceiptNo(String receiptNo) {
		this.receiptNo = receiptNo;
	}

	public Status getStatus() {
		return status;
	}

	public void setStatus(Status status) {
		this.status = status;
	}

	public Integer getVpId() {
		return vpId;
	}

	public void setVpId(Integer vpId) {
		this.vpId = vpId;
	}

	public Date getFpxPaymentDate() {
		return fpxPaymentDate;
	}

	public void setFpxPaymentDate(Date fpxPaymentDate) {
		this.fpxPaymentDate = fpxPaymentDate;
	}

	public String getPaymentStatus() {
		return paymentStatus;
	}

	public void setPaymentStatus(String paymentStatus) {
		this.paymentStatus = paymentStatus;
	}
}