package my.com.pymsoft.safe_g_security.domain.master;

import com.fasterxml.jackson.annotation.JsonView;
import my.com.pymsoft.safe_g_security.domain.master.master.BaseWithDateDomain;
import my.com.pymsoft.safe_g_security.domain.master.master.Status;
import my.com.pymsoft.safe_g_security.view.View;

import javax.persistence.*;

@Entity
@Table(name = "SAFEG_BANK_NAME_LOOKUP")
public class BankNameLookup extends BaseWithDateDomain {

    @JsonView({View.Lookup.class,View.BankNameList.class,View.CompanyPermit.class})
    @Column(name="ID",columnDefinition="NUMBER")
    @Id
    @SequenceGenerator(initialValue=1, sequenceName="BANK_NAME_LOOKUP_SEQ",allocationSize=1,name="bank_name_lookup_seq")
    @GeneratedValue(generator="bank_name_lookup_seq",strategy=GenerationType.SEQUENCE)
    private Integer id;

    @JsonView({View.Lookup.class,View.BankNameList.class,View.CompanyPermit.class})
    @Column(name = "NAME", nullable = true, length = 255)
    private String name;

    @JsonView(View.BankNameList.class)
    @JoinColumn(name="STATUSID")
    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    private Status status;

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public Status getStatus() {
        return status;
    }

    public void setStatus(Status status) {
        this.status = status;
    }
}