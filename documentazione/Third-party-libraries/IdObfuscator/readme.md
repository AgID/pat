IdObfuscator
============

Allows an integer to be hashed, and decoded back.

Original class created by Ray Morgan<br />
http://raymorgan.net/web-development/how-to-obfuscate-integer-ids/

Usage
============
<pre>$hash = IdObfuscator::encode(1234); //returns cRDtpNCeBirJZY$IuwhXSQ
$id = IdObfuscator::decode($hash) //return 1234</pre>