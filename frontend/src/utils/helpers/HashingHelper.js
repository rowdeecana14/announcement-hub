import CryptoJS from 'crypto-js';

const HashingHelper = () => {
  // Define a secret key to be used for encryption
  const secretKey = import.meta.env.VITE_HASH_KEY

  // Hash function that takes in a string and returns a hashed string
  const hashString = (str, length) => {
    const hash = CryptoJS.SHA256(str).toString().substr(0, length);
    return hash;
  }

  // Encryption function that takes in an object and returns an encrypted string
  const encrypt = (obj, length) => {
    const objString = JSON.stringify(obj)
    const encrypted = CryptoJS.AES.encrypt(objString, secretKey).toString().substr(0, length);
    return encrypted
  }

  // Decryption function that takes in an encrypted string and returns a decrypted object
  const decrypt = (encrypted) => {

    try {
        const bytes = CryptoJS.AES.decrypt(encrypted, secretKey);
        const decryptedString = bytes.toString(CryptoJS.enc.Utf8);
        const decryptedObj = JSON.parse(decryptedString);
        return decryptedObj;
      } catch (error) {
        console.error('Error while decrypting data:', error);
        return null;
      }
   
  }

  return {
    hashString,
    encrypt,
    decrypt
  }
}

export default HashingHelper
