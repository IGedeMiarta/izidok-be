<?php

namespace App\Izicrypt;

use App\Izicrypt\Exception\EncryptionFailedException;
use App\Izicrypt\Exception\DecryptionFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Izicrypt
{
    /**
     * encryption secret key.
     * 
     * @var string $secret
     */
    protected $secret = '!zid0ks3cr3tk3y3ncrypti0n#';

    /**
     * request encryption.
     * 
     * @param Illuminate\Http\Request $request
     * @param array $keys
     * @param string $state default 'only'
     * @param boolean $raw
     * @return array
     */
    public function requestEncrypt(Request &$request, array $keys=[], string $state='only', $raw=false) 
    {
        if(is_bool($state)) {
            $raw = $state;
            $state = 'only';
        }

        if(!in_array($state, ['only', 'except'])) throw new \Exception('Request Encryption state either \'only\' or \'except\'');
        $arr = $request->all();

        foreach($arr as $key => $value) {
            if(empty($keys)) {
                $request->request->set($key, $this->encrypt($value));
            }
            elseif($state == 'only' && in_array($key, $keys)) {
                $request->request->set($key, $this->encrypt($value));
            }
            elseif($state == 'except' && !in_array($key, $keys)) {
                $request->request->set($key, $this->encrypt($value));
            }
        }
    }

    /**
     * Item in laravel collection decryption.
     * 
     * @param mixed $item
     * @param array $encrypted
     * @param boolean $raw
     * @param 
     */
    public function itemCollectionDecrypt(&$item, $encrypted=[], $state='only', $raw=false)
    {
        if(is_bool($state)) {
            $raw = $state;
            $state = 'only';
        }

        if(empty($encrypted) && isset($item->encrypted) && $item->encrypted) {
            $encrypted = $item->encrypted;
        }

        if($item instanceof \Illuminate\Database\Eloquent\Model) {
            if(isset($item->encrypted_except) && $item->encrypted_except === true) {
                $state = 'except';
            }

            foreach($item->getAttributes() as $key => $value) {
                if(empty($encrypted)) break;
                if(empty($value)) continue;

                if(in_array($key, $encrypted) && $state=='only') {
                    $item->{$key} = $this->decrypt($value, $raw);
                }
                elseif(!in_array($key, $encrypted) && $state=='except') {
                    $item->{$key} = $this->decrypt($value);
                }
            }
        }
        else {
            foreach($item as $key => $value) {
                if(empty($encrypted)) break;
                if(empty($value)) continue;

                if(in_array($key, $encrypted) && $state=='only') {
                    $item->{$key} = $this->decrypt($value, $raw);
                }
                elseif(!in_array($key, $encrypted) && $state=='except') {
                    $item->{$key} = $this->decrypt($value, $raw);
                }
            }
        }
    }

    /**
     * data encryption.
     * 
     * @param string $data
     * @param boolean $raw
     * @return string
     */
    public function encrypt(string $data, $raw=false) 
    {
        $user = Auth::user();
        $secret = $this->secret;

        if($user && $user->klinik_id) {
            $secret .= md5('!z!d0k' . $user->klinik_id);
        }

        $iv = random_bytes(16);
        $iv_hex = bin2hex($iv);

        if(strlen($data) < 16) {
            $data = str_pad($data, 16, ' ', STR_PAD_LEFT); // need to be ltrim at decrypt
        }

        $options = $raw ? OPENSSL_RAW_DATA : OPENSSL_ZERO_PADDING;
        $result = openssl_encrypt($data, 'aes-256-xts', $secret, $options, $iv);

        if($result === false) {
            throw new EncryptionFailedException("Encryption failed");
        }

        return $iv_hex . $result;
    }


    /**
     * data decryption.
     * 
     * @param string $data
     * @param boolean $raw
     * @return string
     */
    public function decrypt(string $data, $raw=false) 
    {
        $user = Auth::user();
        $secret = '!zid0ks3cr3tk3y3ncrypti0n#';

        if($user && $user->klinik_id) {
            $secret .= md5('!z!d0k' . $user->klinik_id);
        }

        $iv = substr($data, 0, 32);
        $iv_bin = hex2bin($iv);

        $data = substr($data, 32);

        $options = $raw ? OPENSSL_RAW_DATA : OPENSSL_ZERO_PADDING;
        $result = openssl_decrypt($data, 'aes-256-xts', $secret, $options, $iv_bin);

        if($result === false) {
            throw new DecryptionFailedException("Decryption failed");
        }

        // ltrim because str_pad in encrypt
        return ltrim($result);
    }
}