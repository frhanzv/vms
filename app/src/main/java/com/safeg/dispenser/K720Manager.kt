package com.safeg.dispenser

import K720_Package.K720_Serial
import android.content.Context
import android.os.Handler
import android.os.Looper
import android.util.Log
import android.widget.Toast
import com.safeg.utils.Common


class K720Manager(private val context: Context) {

    private val k720 = K720_Serial()
    private var macAddr: Byte = 0x00

    fun connect(port: String = "/dev/ttyS5", baudRate: Int = 9600): Boolean {
        return try {
            K720_Serial.CopyContext(context)
            val ret = K720_Serial.K720_CommOpenWithBaud(port, baudRate)
            if (ret != 0) {
                Log.e("K720", "Failed to open port: $port")
                // Hidden
                // Common.showToast(context, "Failed to open port: $port")
                false
            } else {
                for (i in 0 until 16) {
                    val recordInfo = arrayOfNulls<String>(2)
                    if (K720_Serial.K720_AutoTestMac(i.toByte(), recordInfo) == 0) {
                        macAddr = i.toByte()
                        Log.i("K720", "Connected, MacAddr=$macAddr")
                        return true
                    }
                }
                false
            }
        } catch (e: Exception) {
            // Hidden
            // Common.showToast(context, "Exception during connect")
            Log.e("K720", "Exception during connect", e)
            false
        }
    }

    fun disconnect(): Boolean {
        val ret = K720_Serial.K720_CommClose()
        Log.i("K720", "Disconnected")
        return ret == 0
    }

    fun sendCard(): Boolean {
        val thread = SendCardThread(
            context = context,
            macAddr = macAddr
        )
        thread.start()
        val recordInfo = arrayOfNulls<String>(2)
        val cmd = byteArrayOf(0x46, 0x43, 0x37) // FC7 command
        val ret = K720_Serial.K720_SendCmd(macAddr, cmd, cmd.size, recordInfo)
        return ret == 0
    }

    fun retainCard(): Boolean {
        val recordInfo = arrayOfNulls<String>(2)
        val cmd = byteArrayOf(0x44, 0x42) // DB command
        val ret = K720_Serial.K720_SendCmd(macAddr, cmd, cmd.size, recordInfo)
        return ret == 0
    }

    fun readCardId(): String? {
        val cardId = ByteArray(10)
        val recordInfo = arrayOfNulls<String>(2)
        val ret = K720_Serial.K720_S70GetCardID(macAddr, cardId, recordInfo)
        return if (ret == 0) cardId.joinToString("") { "%02X".format(it) } else null
    }

    fun dispenseCard(): Boolean {
        if (macAddr < 0) {
            // Prod — dispense failed
            Common.showToast(context, "Dispense Failed", Common.ToastType.ERROR)
            return false
        }

        return try {
            reset()
            Thread.sleep(300)
            feed()
            Thread.sleep(300)
            eject()
        } catch (t: Throwable) {
            // Prod — dispense failed
            Common.showToast(context, "Dispense Failed", Common.ToastType.ERROR)
            false
        }
    }

    private fun reset() = sendCmd(byteArrayOf(0x46, 0x43, 0x31)) // FC1
    private fun feed()  = sendCmd(byteArrayOf(0x46, 0x43, 0x35)) // FC5
    private fun move()  = sendCmd(byteArrayOf(0x46, 0x43, 0x37)) // FC7
    private fun eject() = sendCmd(byteArrayOf(0x46, 0x43, 0x33)) // FC3

    private fun sendCmd(cmd: ByteArray): Boolean {
        val info = arrayOfNulls<String>(2)
        val ret = K720_Serial.K720_SendCmd(macAddr, cmd, cmd.size, info)
        return ret == 0
    }
}

class SendCardThread(
    private val context: Context,
    private val macAddr: Byte
) : Thread() {

    @Volatile
    var executeSendCard = true

    override fun run() {

        val stateInfo = ByteArray(20)
        val cardId = ByteArray(10)
        val recordInfo = arrayOfNulls<String>(2)
        val sendBuf = ByteArray(3)

        // 🔕 Hidden — dev only
        // sendMsg("Start card dispensing process", Common.ToastType.INFO)

        while (executeSendCard) {

            sleepSafe(800)

            // 1️⃣ Sensor query
            val ret = K720_Serial.K720_SensorQuery(macAddr, stateInfo, recordInfo)
            if (ret != 0) {
                // Hidden
                // sendMsg("Sensor query error", Common.ToastType.ERROR)
                Log.e("K720", "Sensor query error ret=$ret")
                continue
            }

            // 2️⃣ Card box empty — ✅ PROD visible
            if ((stateInfo[3].toInt() and 0x08) == 0x08) {
                sendMsg(
                    "Out of Card — Please contact staff.",
                    Common.ToastType.WARNING,
                    Toast.LENGTH_LONG
                )
                sleepSafe(2500)
                sendMsg(
                    "Out of Card — Please contact staff.",
                    Common.ToastType.ERROR,
                    Toast.LENGTH_LONG
                )
                break
            }

            // 3️⃣ Send card to reader (FC7)
            // Hidden
            // sendMsg("Sending card to reader...", Common.ToastType.INFO)

            sendBuf[0] = 0x46
            sendBuf[1] = 0x43
            sendBuf[2] = 0x37

            if (K720_Serial.K720_SendCmd(macAddr, sendBuf, 3, recordInfo) != 0) {
                // PROD visible — dispense failed
                sendMsg(
                    "Dispense Failed — Please try again.",
                    Common.ToastType.ERROR,
                    Toast.LENGTH_LONG
                )
                Log.e("K720", "SendCmd FC7 failed")
                continue
            }

            sleepSafe(1500)

            // 4️⃣ Read card ID — hidden from prod
            val readRet = K720_Serial.K720_S70GetCardID(macAddr, cardId, recordInfo)
            if (readRet == 0) {
                // Hidden — (UID not needed by user)
                // sendMsg("Card UID: ${byteArrayToHex(cardId, 4)}", Common.ToastType.INFO)
                Log.d("K720", "Card UID: ${byteArrayToHex(cardId, 4)}")
            } else {
                // Hidden
                // sendMsg("Failed to read card UID", Common.ToastType.WARNING)
                Log.w("K720", "Failed to read card UID")
            }

            // 5️⃣ Eject card — hidden from prod
            // Hidden
            // sendMsg("Ejecting card...", Common.ToastType.INFO)

            sendBuf[2] = 0x30 // FC0
            K720_Serial.K720_SendCmd(macAddr, sendBuf, 3, recordInfo)

            // PROD visible — success
            sendMsg(
                "Card Dispensed Successfully",
                Common.ToastType.SUCCESS,
                Toast.LENGTH_LONG
            )
            break
        }
    }

    private fun sendMsg(text: String, type: Common.ToastType, duration: Int = Toast.LENGTH_SHORT) {
        Handler(Looper.getMainLooper()).post {
            Common.showToast(context, text, type, duration)
        }
    }

    private fun sleepSafe(ms: Long) {
        try { Thread.sleep(ms) } catch (_: Exception) {}
    }

    private fun byteArrayToHex(data: ByteArray, len: Int): String =
        data.take(len).joinToString(" ") { "%02X".format(it) }
}