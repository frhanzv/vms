package my.com.pymsoft.safe_g_security.domain.master;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.SequenceGenerator;
import javax.persistence.Table;

import com.fasterxml.jackson.annotation.JsonView;

import lombok.Getter;
import lombok.Setter;
import my.com.pymsoft.safe_g_security.domain.master.master.BaseWithDateDomain;
import my.com.pymsoft.safe_g_security.view.View;

@Getter
@Setter
@Entity
@Table(name = "SAFEG_FPX_REQUEST")
public class FPXIntegrationTransaction extends BaseWithDateDomain{
	    @Column(name="ID",columnDefinition="NUMBER")
	    @Id
	    @SequenceGenerator(initialValue=1, sequenceName="FPX_REQUEST_SEQ",allocationSize=1,name="fpx_request_seq")
	    @GeneratedValue(generator="fpx_request_seq",strategy=GenerationType.SEQUENCE)
	    private Integer id;
	    
	    @Column(name = "fpx_sellerTxnTime", nullable = true, length = 255)
	    private String fpx_sellerTxnTime;

	    @Column(name = "fpx_sellerExOrderNo", nullable = true, length = 255)
	    private String fpx_sellerExOrderNo;
	    
	    @Column(name = "fpx_sellerOrderNo", nullable = true, length = 255)
	    private String fpx_sellerOrderNo;
	    
	    @Column(name = "fpx_sellerId", nullable = true, length = 255)
	    private String fpx_sellerId;

	    @Column(name = "fpx_sellerBankCode", nullable = true, length = 255)
	    private String fpx_sellerBankCode;
	    
	    @Column(name = "fpx_txnCurrency", nullable = true, length = 255)
	    private String fpx_txnCurrency;

	    @Column(name = "fpx_txnAmount", nullable = true, length = 255)
	    private String fpx_txnAmount;
	    
	    @Column(name = "fpx_buyerEmail", nullable = true, length = 255)
	    private String fpx_buyerEmail;

	    @Column(name = "fpx_buyerBankId", nullable = true, length = 255)
	    private String fpx_buyerBankId;
	    
	    @Column(name = "fpx_productDesc", nullable = true, length = 255)
	    private String fpx_productDesc;

	    @Column(name = "fpx_version", nullable = true, length = 255)
	    private String fpx_version;
	    
	    @Column(name = "fpx_buyerAccNo", nullable = true, length = 255)
	    private String fpx_buyerAccNo;

	    @Column(name = "fpx_buyerBankBranch", nullable = true, length = 255)
	    private String fpx_buyerBankBranch;
	    
	    @Column(name = "fpx_buyerIban", nullable = true, length = 255)
	    private String fpx_buyerIban;

	    @Column(name = "fpx_buyerId", nullable = true, length = 255)
	    private String fpx_buyerId;
	    
	    @Column(name = "fpx_makerName", nullable = true, length = 255)
	    private String fpx_makerName;
	    
	    @Column(name = "RECEIPTNO", nullable = true, length = 255)
	    private String receiptNo;
	

}
