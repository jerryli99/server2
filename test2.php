<?php
class PublicPrivateKey{
    public static function generateKeyPair(){
        //use RSA algorithm
        $res = openssl_pkey_new([
            "private_key_bits"=>2048,
            "private_key_type"=>OPENSSL_KEYTYPE_RSA,
        ]);

        //get private key. The function has $privateKey as the output of private key.
        openssl_pkey_export($res, $privateKey);

        //return public key, private key.
        $publicKey = openssl_pkey_get_details($res)['key'];
        return [$publicKey, $privateKey];
    }

    public static function encrypt($sign, $privateKey){
        openssl_private_encrypt($sign, $crypted, $privateKey);
        return base64_encode($crypted);
    }

    public static function decrypt($crypted, $publicKey){
        openssl_public_decrypt(base64_decode($crypted), $decrypted, $publicKey);
        return $decrypted;
    }

    public static function isValidKey($sign, $crypted, $publicKey): bool{
        return $sign == self::decrypt($crypted, $publicKey);
    }
}


[$publicKey, $privateKey] = PublicPrivateKey::generateKeyPair();
echo [$publicKey, $privateKey];