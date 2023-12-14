const multer = require('multer');
const path = require('path');
const { v4: uuidv4 } = require('uuid');
const fs = require('fs');

function createStorage(directoryPath = 'uploads/default') {
  return multer.diskStorage({
    destination: function (req, file, cb) {
      fs.mkdirSync(directoryPath, { recursive: true });
      cb(null, directoryPath);
    },
    filename: function (req, file, cb) {
      cb(null, file.fieldname + '-' + uuidv4() + path.extname(file.originalname)); // Appending extension
    },
  });
}

function uploadFile(type = /jpg|jpeg|png|gif|mp4|MP4|mov|avi|3gp|ts|m3u8|WebM|webm/, fileSize = 200000000) {

  try {
    var userImageDirectoryPath = 'uploads/posts';

    return multer({
      storage: createStorage(userImageDirectoryPath),
      //limits: { fileSize: fileSize },
      fileFilter: (req, file, cb) => {
        const validFileTypes = type;
        const extname = validFileTypes.test(path.extname(file.originalname).toLowerCase());
        if (extname === true) {
          return cb(null, true);
        } else {
          return cb("Error:File not accepted!");
        }
      },
    });
  } catch (error) {
    Error.payload = error.errors ? error.errors : error.message;
    throw new Error();
  }
}


module.exports = {
  uploadFile,
  
};