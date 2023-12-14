
exports.validateRequest = (param, schema) => {
  const options = {
    abortEarly: false,
    allowUnknown: true,
    stripUnknown: true,
  };
  
  const { error } = schema.validate(param, options);
  if (error) {
    let object = error.details[0].message;
  
    return object;
  }
};
exports.successResponse = (req, res, data, msg, code = 200) => {

  if (data)
    var resObj = {
      statusCode: code,
      message: msg,
      data: data,
    };
  else
    var resObj = {
      statusCode: code,
      message: msg,
    };

  res.status(200);
  res.send(resObj);
};

exports.errorResponse = (req, res, msg, code = 500) => {
  res.status(code);
  res.send({
    statusCode: code,
    message: msg,
  });
};

