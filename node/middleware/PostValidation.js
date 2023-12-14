const Joi = require("joi");
const helpers = require("../helpers/response.helpers");
var Post = require("../models/PostModel");

const schema = Joi.object({

  description: Joi.string().required().messages({
    "string.empty": `Post description is required.`,
    "any.required": `Post description is required.`,
  }),
  
  gallery: Joi.array().min(1).required().messages({
    "array.base": "Items must be an array.",
    "array.empty": "Items is required.",
    "array.min": "Items must have at least 1 element.",
    "any.required": `Gallery is required.`,
  }),
});

const updateschema = Joi.object({

  description: Joi.string().required().messages({
    "string.empty": `Post description is required.`,
    "any.required": `Post description is required.`,
  }),
  
  
});

const likeSchema = Joi.object({
  post_id: Joi.string().regex(/^[A-Za-z0-9]{24}$/).required()
  .messages({
    'string.pattern.base': 'Invalid post_id format. It should be a 24-character alphanumeric string.',
    'any.required': 'post_id is required.'
  }),
  status: Joi.number().valid(1, 2).required().messages({
    "number.base": `Status must be a number.`,
    "number.empty": `Status is required.`,
    "any.required": `Status is required.`,
    "any.only": "Status must be 1, or 2.",
  })
  
})

const commentSchema = Joi.object({
  post_id: Joi.string().regex(/^[A-Za-z0-9]{24}$/).required()
  .messages({
    'string.pattern.base': 'Invalid post_id format. It should be a 24-character alphanumeric string.',
    'any.required': 'post_id is required.'
  }),
  comment: Joi.string().required().messages({
    "string.empty": `Post comment is required.`,
    "any.required": `Post comment is required.`,
  }),
  
})

const reportSchema = Joi.object({
  post_id: Joi.string().regex(/^[A-Za-z0-9]{24}$/).required()
  .messages({
    'string.pattern.base': 'Invalid post_id format. It should be a 24-character alphanumeric string.',
    'any.required': 'post_id is required.'
  }),
  reason: Joi.string().required().messages({
    "string.empty": `Post reason is required.`,
    "any.required": `Post reason is required.`,
  }),
  
})


exports.create = async (req, res, next) => {
  console.log("postValidation@create");

  const error = helpers.validateRequest(req.body, schema);

  if (error) {
    return helpers.errorResponse(req, res, error, 400);
  }

  return next();
};

exports.update = async (req, res, next) => {
  console.log("postValidation@update");

  const error = helpers.validateRequest(req.body, updateschema);

  if (error) {
    return helpers.errorResponse(req, res, error, 400);
  }

  return next();
};

exports.like = async (req, res, next) => {
  console.log("postValidation@like");

  const error = helpers.validateRequest(req.body, likeSchema);

  if (error) {
    return helpers.errorResponse(req, res, error, 400);
  }

  return next();
};

exports.comment = async (req, res, next) => {
  console.log("postValidation@comment");

  const error = helpers.validateRequest(req.body, commentSchema);

  if (error) {
    return helpers.errorResponse(req, res, error, 400);
  }

  let findPost = await Post.findOne({_id:req.body.post_id})
  if (!findPost) {
    return helpers.errorResponse(req, res, 'Post id not found', 400);
  }

  return next();
};


exports.report = async (req, res, next) => {
  console.log("postValidation@report");

  const error = helpers.validateRequest(req.body, reportSchema);

  if (error) {
    return helpers.errorResponse(req, res, error, 400);
  }

  let findPost = await Post.findOne({_id:req.body.post_id})
  if (!findPost) {
    return helpers.errorResponse(req, res, 'Post id not found', 400);
  }

  return next();
};
