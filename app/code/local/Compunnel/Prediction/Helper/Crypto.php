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

    private function initializeParameters()
    {
        $mcryptIvSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $rawIv = mcrypt_create_iv($mcryptIvSize, MCRYPT_DEV_URANDOM);
        $this->iv = bin2hex($rawIv);
        $this->iv = substr($this->iv, 0, 16);
        Mage::log($this->iv, null, 'prediction.log');
        $this->secretKey = '646959716559664a646959716559664a';
        Mage::log($this->secretKey, null, 'prediction.log');
    }

    private function hmac($message)
    {
        return hash_hmac(self::HASH_ALGORITHM, $message, $this->secretKey, true);
    }
}
