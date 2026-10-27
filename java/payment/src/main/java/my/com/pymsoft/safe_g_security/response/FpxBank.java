package my.com.pymsoft.safe_g_security.response;

import lombok.Getter;
import lombok.Setter;

@Getter
@Setter
public class FpxBank {
	private String bankId;
	private String status;
	public FpxBank(String bankId, String status) {
		super();
		this.bankId = bankId;
		this.status = status;
	}
	
}
