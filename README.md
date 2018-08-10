# IPSEcovacs

This is supose to become a library for controlling and monitoring a Ecovacs Vacum cleaner via IP-Symcon

As example I take [Sucks](https://github.com/wpietri/sucks) this is written in Phyton so it needs to be rewritten in PHP.

## Passed steps:
* Being able to create good URL > URL looks good now but I have problems with logging in. Thit is probably caused by the base64 encoding
* Rebuild sign() function

## Next Steps
* Rebuild encrypt() function 
    * Gettin a working RSA encryption
    * gettin PHP generate the same base64 string as Phyton does
* being able to login to the api
* get the key for loging in to the MMQT servers
