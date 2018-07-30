<?php
/**
 * PHP version 5
 *
 * @category  Compunnel
 * @package   Compunnel_Prediction
 * @author    Prateek Agrawal <prateek.agarwal@compunnel.com>
 * @copyright 2018 Compunnel (https://www.compunnel.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 * @link      https://bitbucket.org/prateekatcompunnel/apac-prediction
*/

/**
 * Prediction data helper
 *
 * @category Compunnel
 * @package  Compunnel_Prediction
 * @author   Prateek Agrawal <prateek.agarwal@compunnel.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License
 * @link     https://bitbucket.org/prateekatcompunnel/apac-prediction
 */

class Compunnel_Prediction_Helper_Crypto extends Mage_Core_Helper_Abstract
{
    const HASH_ALGORITHM = 'SHA256';
    const ENCRYPTION_ALGORITHM = 'AES-256-CBC';

    private $secretKey;
    private $iv;
    private $encryptedMessage;
    private $hmac;

    public function encrypt($data)
    {
        $this->initializeParameters();
        $encryptedMessage = openssl_encrypt($data, self::ENCRYPTION_ALGORITHM, $this->secretKey, 0, $this->iv);
        $hmac = $this->hmac($encryptedMessage);
        return implode(
            ':',
            array(
                base64_encode($hmac),
                base64_encode($this->iv),
                base64_encode($encryptedMessage)
            )
        );
    }

    public function decrypt($data)
    {
        $this->initializeParameters();
        $this->breakDownResponse($data);
        $decryptedMessage = '';
        if ($this->messageAuthenticated()) {
            $decryptedMessage = openssl_decrypt($this->encryptedMessage, self::ENCRYPTION_ALGORITHM, $this->secretKey, 0, $this->iv);
        }
        return $decryptedMessage;
    }

    private function breakDownResponse($data)
    {
        $messageParts = explode(":", $data);
        $this->iv = base64_decode($messageParts[1]);
        $this->encryptedMessage = $messageParts[2];
        $this->hmac = base64_decode($messageParts[0]);
    }

    private function messageAuthenticated()
    {
        $calculatedHmac = $this->hmac(base64_encode($this->iv) . ":" . $this->encryptedMessage, true);
        if ($calculatedHmac == $this->hmac) {
            return true;
        } else {
            return false;
        }
    }

    private function initializeParameters()
    {
        $mcryptIvSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $rawIv = mcrypt_create_iv($mcryptIvSize, MCRYPT_DEV_URANDOM);
        $this->iv = bin2hex($rawIv);
        $this->iv = substr($this->iv, 0, 16);
        $this->secretKey = '646959716559664a646959716559664a';
    }

    private function hmac($message, $rawOutput = true)
    {
        return hash_hmac(self::HASH_ALGORITHM, $message, $this->secretKey, $rawOutput);
    }
}
