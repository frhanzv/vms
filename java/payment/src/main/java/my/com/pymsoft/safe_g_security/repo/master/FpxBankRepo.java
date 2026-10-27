package my.com.pymsoft.safe_g_security.repo.master;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import my.com.pymsoft.safe_g_security.domain.master.FpxBanks;

public interface FpxBankRepo extends JpaRepository<FpxBanks,Integer>{

	@Query("SELECT b.displayName FROM FpxBanks b " +
            "WHERE b.bankId = :bankid and type=:type")
	String findByBankId(@Param("bankid") String bankid,@Param("type") String type);

}
