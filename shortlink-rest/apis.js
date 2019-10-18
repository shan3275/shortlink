const basicAuth		= require('basic-auth');
const fns			= require('./dbfuncs');
const crypto		= require('crypto');

process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';

const auth = (req, res, next) => {
	function unauthorized(res){
		res.set('WWW-Authenticate', 'Basic realm=Input User&Password');
		return res.sendStatus(401);
	}
	
	const user = basicAuth(req);
	if (!user || !user.name || !user.pass) {
		return unauthorized(res);
	}
	
	const now = new Date();
	now.setHours(0);
	now.setMinutes(0);
	now.setSeconds(0);
	now.setMilliseconds(0);

	if(user.name == 'xXl' && user.pass === md5('8i6Y4r7U' + now.valueOf())){
		return next();
	}
	return unauthorized(res);
};
function md5(str){
	return crypto.createHash('md5').update(str).digest('hex');
}
function addApis(app, db){
	app.get('/showpass', async(req, res) => {
		const d = new Date();
		d.setHours(0);
		d.setMinutes(0);
		d.setSeconds(0);
		d.setMilliseconds(0);
		res.status(200).json({pass: md5('8i6Y4r7U' + d.valueOf())});
	});
	app.post('/shortlink/query', auth, async (req, res) => {
		const data = await fns.query(db, req.body.user, req.body.pass);
		res.status(200).json(data);
	});
	app.post('/shortlink/enable', auth, async (req, res) => {
		const data = await fns.enable(db, req.body.user, req.body.pass, req.body.short, req.body.link);
		res.status(200).json(data);
	});
}

module.exports = {
	addApis
}
