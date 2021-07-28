<?php
require_once 'test1.php';
//quick test.
$data1 = 'jerry oqiwuehro weiur weriu eriuh weriouh ri<werw/ertwpoertw r>wertwerptower]t[weprt/w[ert/w[erpo/tw24524';
$data2 = 'Li';
$data3 = 'is';
$data4 = 'my';
$data5 = 'name';
// echo 'Original hash:<br>' . ProofOfwork::gethash($data) . '<hr>';
// $result = ProofOfWork::findNonce($data);
// echo '<br>' . $result;

// $block1 = new Block($data1, null);
// $block1->setBlockNum(1);


// $block2 = new Block($data2, $block1);
// $block2->setBlockNum(2);


// $block3 = new Block($data3, $block2);
// $block3->setBlockNum(3);


// $block4 = new Block($data4, $block3);
// $block4->setBlockNum(4);



// echo '<div id="blocks">' . $block1->__toString() . '<br>' . 
//       $block2->__toString() . '<br>' . $block3->__toString() . 
//      '<br>' . $block4->__toString() . '</div>';

$block = new BlockChain($data1, null, 'jerry');
$block->signTransaction();
$block->add($data2);
$block->signTransaction();
$block->add($data3);
$block->signTransaction();
$block->add($data4);
$block->signTransaction();
// print $block . "\n";
// [$block->blocks[1], $block->blocks[3]] = [$block->blocks[3], $block->blocks[1]];
print $block->isValid();
print $block . "\n" ;
// var_dump($block);
