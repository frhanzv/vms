package my.com.pymsoft.safe_g_security.repo.master;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;

import my.com.pymsoft.safe_g_security.domain.master.PaymentDetail;

public interface PaymentDetailRepo extends JpaRepository<PaymentDetail, Integer> {

	List<PaymentDetail> findByHeaderId(Integer id);

}
