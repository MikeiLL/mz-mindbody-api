<?php
/**
 * @license MIT
 *
 * Modified by Mike iLL Kilmer on 24-November-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace MZoo\MzMindbody\Dependencies\Defuse\Crypto;

/**
 * Class DerivedKeys
 * @package Defuse\Crypto
 */
final class DerivedKeys
{
    /**
     * @var string
     */
    private $akey = '';

    /**
     * @var string
     */
    private $ekey = '';

    /**
     * Returns the authentication key.
     * @return string
     */
    public function getAuthenticationKey()
    {
        return $this->akey;
    }

    /**
     * Returns the encryption key.
     * @return string
     */
    public function getEncryptionKey()
    {
        return $this->ekey;
    }

    /**
     * Constructor for DerivedKeys.
     *
     * @param string $akey
     * @param string $ekey
     */
    public function __construct($akey, $ekey)
    {
        $this->akey = $akey;
        $this->ekey = $ekey;
    }
}
