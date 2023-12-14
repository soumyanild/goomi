const { uploadFile } = require('../utils/upload.utils')
module.exports = function (app) {
    var Post = require('../controllers/PostController');
    var postValidation = require('../middleware/PostValidation');
    var auth = require('../middleware/authorize')

    //new api integrated
    app.post('/post/create',[auth.auth, postValidation.create],  Post.create);
    app.put('/post/put/:postId',[auth.auth, postValidation.update],  Post.update)
    app.get('/post/get', [auth.auth], Post.getAll);
    app.get('/post/:postId',[auth.auth], Post.get_post);
    
    app.post('/post/like',[auth.auth, postValidation.like], Post.like )
    app.post('/post/comment',[auth.auth, postValidation.comment], Post.comment )
    app.post('/post/report',[auth.auth, postValidation.report], Post.report )
    app.get('/post/report/get', [auth.auth], Post.getAllReport);
    app.get('/post/comment/:postId',[auth.auth], Post.getAllComment);
    app.delete('/post/delete/:postId',[auth.auth], Post.delete);
    app.post('/post/upload-file', [auth.auth], uploadFile().single('uploadfile'), Post.uploadFile)

   
}