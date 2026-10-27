package my.com.pymsoft.safe_g_security.domain.master;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.SequenceGenerator;
import javax.persistence.Table;

import lombok.Getter;
import lombok.Setter;

@Getter
@Setter
@Entity
@Table(name="FPX_BANKS")
public class FpxBanks {
	
	    @Id
	    @SequenceGenerator(initialValue=1, sequenceName="FPX_BANKS_SEQ",allocationSize=1,name="fpx_seq")
		@GeneratedValue(generator="fpx_seq",strategy=GenerationType.SEQUENCE)
	    private Integer id;
	  
    @Column(name = "BANK_ID")
    private String bankId;

    @Column(name = "BANK_NAME")
    private String bankName;

    @Column(name = "DISPLAY_NAME")
    private String displayName;

    @Column(name = "TYPE")
    private String type;
	 
}
