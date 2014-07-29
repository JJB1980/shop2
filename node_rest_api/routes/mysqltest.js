
var express = require('express');
var router = express.Router();
var mysql = require('mysql');

router.get('/', pullRequest);
router.get('/:id', pullRequest);

function pullRequest(req, res) {
    //console.log(req.params);
    var connection = mysql.createConnection({
        host     : 'localhost',
        user     : 'CLI1SQLUSR',
        password : 'xxx321',
        database : 'cli1'
    });
    connection.connect(function(err, connection) {
        if (err) {
            console.error('CONNECTION error: ',err);
            res.statusCode = 503;
            res.send({
                result: 'error',
                err:    err.code
            });
        }
    });          
    var sql = 'SELECT * FROM Inventory LIMIT 20';
    if (req.params.id) {
        sql = 'select * from Inventory where ID = ' + req.params.id;
    }
    connection.query(sql,
                     function(err, rows, fields) {
        if (err) {
            console.error(err);
            res.statusCode = 500;
            res.send({
                result: 'error',
                err:    err.code
            });
        }
        res.send({
            sql: sql,
            result: 'success',
            err:    '',
            json:   rows,
            length: rows.length,
            lastId: req.session.lastId
        });
    });
    if (req.params.id && !req.session.lastId)
        req.session.lastId = req.params.id;

    connection.end();
}

module.exports = router;
