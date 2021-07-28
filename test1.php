<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        #block{
            margin: 1rem auto;
            width: 40%;
            height: fit-content;
            border: 1px solid black;
            padding: 1rem;
        }

        .arrow{
            text-align: center;
            font-size: 20px;
        }
    </style>
</head>
<body>
    
</body>
</html>

<?php
date_default_timezone_set('America/New_York');
//their is a proof of work protocal (simplified)
include_once 'test2.php';
class ProofOfWork{
    public static function getHash($data){
        return hash('sha256', $data);
    }

    //find the nonce
    public static function findNonce($data){
        $nonce = 0;
        while(!self::isValidNonce($data, $nonce)){
            ++$nonce;
        }
        return $nonce . '<br>Valid nonce: ' . self::isValidNonce($data, $nonce) . 
               '<br>Mine challenge:<br><mark>' . hash('sha256', $data . $nonce) . '</mark>';
    }

    //We don't change the hash. 
    //We find the fist n zeros by adding data and nonce together, and then hash again
    //until we have the matching n zeros from index[0] to index[n] of hash($data); with
    //all of that, we can tell that the miner has been using his or her computer to calculate
    //the nonce value starting from 0 to 16^n possibilities(since sha256 is in hex format). 
    //Thus, proof of work is done.  
    public static function isValidNonce($data, $nonce){
        return 0 === strpos(hash('sha256', $data . $nonce), '00');
    }
}


class Block{
    private $previousHash;
    private $hash;
    private $nonce;
    private $blockNumber;
    private $sign;
    private $publicKey;
    private $crypted;
    public $data;

    public function __construct($data, ?Block $previousHash){
        $this->previousHash = $previousHash ? $previousHash->hash : 'Genesis block';
        $this->data = $data;
        $this->mine();
    }

    public function mine(){
        $data = $this->data . $this->previousHash . $this->blockNumber;
        $this->nonce = ProofOfWork::findNonce($data);
        $this->hash = ProofOfWork::getHash($data . $this->nonce);
    }

    public function isVaild(): bool{
        return ProofOfWork::isValidNonce($this->data . $this->previousHash . $this->blockNumber, $this->nonce)
               && PublicPrivateKey::isValidKey($this->sign, $this->crypted, $this->publicKey);
    }

    public function setBlockNum($blockNum){
        return $this->blockNumber = $blockNum;
    }

    public function getBlockNum(){
        return $this->blockNumber;
    }

    public function getHash(){
        return $this->hash;
    }

    public function getPreviousHash(){
        return $this->previousHash;
    }

    public function __toString(){
        return '<div class="block" id="block">Block#: '  .  $this->blockNumber . 
               '<br>Nonce: ' . $this->nonce . '<br>Data: ' . 
                $this->data . '<br>PreviousHash:<br><mark> ' .
                $this->previousHash . '</mark><br>Hash:<br><mark>' . 
                $this->hash .
                '</mark>
                </div><div class="arrow"><strong>&#8593;...</strong></div>';
    }
}

class BlockChain{
    //put blocks in array
    public $blocks = [];
    private $sign;
    private $publicKey;
    private $crypted;

    public function __construct($data, $sign){
        $this->blocks[] = new Block($data, null);
        //set the genesis blockNumber to zero
        $this->blocks[0]->setBlockNum(0);
        //
        $this->sign = $sign;
    }

    //add block to array blocks[], including the blockNumber
    public function add($data){
        $totalBlocks = count($this->blocks);
        $this->blocks[] = new Block($data, $this->blocks[$totalBlocks-1]);
        $this->blocks[$totalBlocks]->setBlockNum($totalBlocks);
    }


    public function signTransaction(){
        [$publicKey, $privateKey] = PublicPrivateKey::generateKeyPair();
        $this->publicKey = $publicKey;
        echo '<br>Your public key: <mark>' . $this->publicKey . '</mark></br>';
        $this->crypted = PublicPrivateKey::encrypt($this->sign, $privateKey);
        return '<br><mark>Your cryoted signature:' . $this->crypted . '</mark><br>';
    }

    public function isValid(): bool{
        foreach($this->blocks as $i => $block){
            //checking two blocks' hash and previous hash
            if($i != 0 && $this->blocks[$i-1]->getHash() != $block->getPreviousHash()){
                echo '<h1 style="text-align:center">Not a valid chain. Check Block#'  .  
                     $this->blocks[$i-1]->getBlockNum() . ' hashes and the next block hashes.</h1><br>';
                return false;
            }
        }
        echo '<h1 style="text-align:center">Congradulations! This is a valid chain.</h1>';
        return true;
    }

    public function __toString(){
        // implode — Join array elements with a string
        return implode("\n\n" , $this->blocks);
    }
}



