package my.com.pymsoft.safe_g_security.domain.master;

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
@Table(name = "SAFEG_PAYMENT_DETAIL")
public class PaymentDetail extends BaseWithDateDomain {

    @JsonView(View.PaymentDetail.class)
    @Column(name="ID",columnDefinition="NUMBER")
    @Id
    @SequenceGenerator(initialValue=1, sequenceName="PAYMENT_DETAIL_SEQ",allocationSize=1,name="payment_detail_seq")
    @GeneratedValue(generator="payment_detail_seq",strategy=GenerationType.SEQUENCE)
    private Integer id;

    @JsonView(View.PaymentDetail.class)
    @Column(name = "TYPE", nullable = true, length = 255)
    private String type;
    
    @JsonView(View.PaymentDetail.class)
    @Column(name = "AMOUNT",columnDefinition="NUMBER")
    private Integer amount;   
    
    @JsonView(View.PaymentDetail.class)
    @Column(name = "TOTALAMOUNT",columnDefinition="NUMBER")
    private Integer totalAmount; 
    
    @JsonView(View.PaymentDetail.class)
    @Column(name = "COUNT", columnDefinition="NUMBER")
    private Integer count;
    
    @JsonView(View.PaymentDetail.class)
    @Column(name = "HEADER_ID", columnDefinition="NUMBER")
    private Integer headerId;

	@JsonView(View.PaymentDetail.class)
    @JoinColumn(name="STATUSID")
    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    private Status status;

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

	public Integer getAmount() {
		return amount;
	}

	public void setAmount(Integer amount) {
		this.amount = amount;
	}

	public Integer getTotalAmount() {
		return totalAmount;
	}

	public void setTotalAmount(Integer totalAmount) {
		this.totalAmount = totalAmount;
	}

	public Integer getCount() {
		return count;
	}

	public void setCount(Integer count) {
		this.count = count;
	}

	public Status getStatus() {
		return status;
	}

	public void setStatus(Status status) {
		this.status = status;
	}


    public Integer getHeaderId() {
		return headerId;
	}

	public void setHeaderId(Integer headerId) {
		this.headerId = headerId;
	}

	
}