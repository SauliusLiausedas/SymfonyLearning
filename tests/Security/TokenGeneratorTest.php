<?php


namespace App\Tests\Security;


use App\Security\TokenGenerator;

class TokenGeneratorTest extends \PHPUnit\Framework\TestCase
{
    public function testTokenGeneration()
    {
        $length = 30;
        $tokenGen = new TokenGenerator();
        $token = $tokenGen->getRandomSecureToken($length);

        $this->assertEquals($length, strlen($token));
        $this->assertTrue(ctype_alnum($token), 'Token contains incorrect characters');
    }
}