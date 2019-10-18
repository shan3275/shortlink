const crypto = require('crypto');

function md5(str){
	return crypto.createHash('md5').update(str).digest('hex');
}
async function checkPass(db, user, pass){
	const Users = db.collection('users');
	const cnt = await Users.countDocuments({_id: user, pass: md5(pass + 'abrkDbr')});
	return cnt != 0;
}
async function query(db, user, pass){
	if(!(user && pass)) return {code: 100, msg: 'invalid parameter'};
	if(!await checkPass(db, user, pass)) return {code: 101, msg: 'invalid user or password'};

	const Shorts = db.collection('shorts');
	const Links = db.collection('links');

	const shorts = await Shorts.find({user: user}).toArray();
	for(let i in shorts){
		shorts[i].links = await Links.find({short: shorts[i]._id}).toArray();
		shorts[i].short = shorts[i]._id;
		delete shorts[i]._id;
		shorts[i].expire = shorts[i].exp;
		delete shorts[i].exp;
		delete shorts[i].user;
		for(let j in shorts[i].links){
			delete shorts[i].links[j].created;
			delete shorts[i].links[j].short;
			delete shorts[i].links[j]._id;
		}
	}
	return {code: 0, shorts: shorts};
}
async function enable(db, user, pass, short, link){
	if(!(user && pass && short && link)) return {code: 100, msg: 'invalid parameter'};
	if(!await checkPass(db, user, pass)) return {code: 101, msg: 'invalid user or password'};

	const Shorts = db.collection('shorts');
	const Links = db.collection('links');

	const cnt = await Links.count({short: short, url: link});
	if(cnt == 0){
		return {code: 102, msg: 'link not found'};
	}
	await Links.updateMany({short: short}, {$unset: {enabled: 1}});
	await Links.updateOne({short: short, url: link}, {$set: {enabled: true}});
	await Shorts.updateOne({_id: short}, {$set: {url: link, updated: new Date()}});
	return {code: 0, msg: 'short link enabled'};	
}
module.exports = {
	query,
	enable
}
