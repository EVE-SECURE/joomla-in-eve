# API Key Encryption #

## Requirements ##

  * mcrypt PHP module

## How to ##
  1. **BACKUP #eve\_accounts table**
  1. Only for older versions: check length of apiKey field in DB. If it's only 64, change it to 255. Encrypted keys are longer.
  1. Go to Components-> EVE -> Overview
  1. Click on Encryption
  1. Select Cipher and Mode (ECB is recommended)
  1. Enter Pass Phrase
  1. Choose, if people with backend access can view API key (Account Edit Form)
  1. Confirm

The component will try create new config file in administrator/com\_eve/configs directory. If the directory isn't writable, you'll have to copy contents from textarea and create it manually.