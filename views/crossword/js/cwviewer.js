/*
 * (C)Copyright Entreveloper.com
 * A simple crossword viewer and interactive solver
 *  License: MIT
 * -----------------------------------
 *
 * MIT License
 *
 * Copyright Entreveloper.com (https://entreveloper.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
function Crossword(canvas, callback) {
    this.canvas = canvas;
    this.context = canvas.getContext('2d');
    this.callback = callback;

    this.canvas.addEventListener('mousemove', this.mousePos, false);
    this.canvas.addEventListener('mousedown', this.mouseDown, false);
    this.canvas.addEventListener('keydown', this.keyDown, false);
    document.addEventListener('keydown', this.keyDown, false);
    //this.canvas.addEventListener('input', this.input, false);
    this.fillColor = 'black';
    this.font = '10pt Calibri';
    this.useCursor = true;
    this.isMobile = false;
}

Crossword.prototype = {
    constructor: Crossword,
    mousePos:function (evt)  {
        var rect = this.getBoundingClientRect(), x = evt.clientX - rect.left, y = evt.clientY - rect.top;
        return {
            x: evt.clientX - rect.left,
            y: evt.clientY - rect.top
        };
    },
	setMobile: function(val) {
    	cw.isMobile = val;
	},
    // keypress: function (evt) {
    //     if (cw.isMobile && evt.mbke == null) return;
    // },
    keyDown:function (evt) {
    	if (cw.isMobile && evt.mbke == null) return; // workaround for double key event in iPad, after custom mobile handling
        if(cw.xpos<cw.nx && cw.ypos<cw.ny){
        	var letter = "";
        	if (evt.key != null) {
        		letter = evt.key;
			} else if (evt.keyCode != null) { // for iOS, from what I tested on the Mac
        		letter = String.fromCharCode(evt.keyCode);
			}
            if (cw.cwMask[cw.xpos][cw.ypos]!==1 && cw.isletter(letter)){
                cw.cwMask[cw.xpos][cw.ypos]= letter;
                cw.drawBoard();
                if (cw.dir === 0) {
                    cw.xpos++;
                } else if (cw.dir === 1) {
                    cw.ypos++;
                }
                cw.showCursor();
                cw.drawIdxs();
            } else {
            	if (evt.keyCode != null && evt.keyCode == 8) {
					if (cw.dir === 0 && cw.xpos > 0) {
						cw.xpos--;
					} else if (cw.dir === 1 && cw.ypos > 0) {
						cw.ypos--;
					}
					if (cw.cwMask[cw.xpos][cw.ypos]!==1) {
						cw.cwMask[cw.xpos][cw.ypos]= 0;
						cw.drawBoard();
					}
					cw.showCursor();
					cw.drawIdxs();
				}
			}
        }
    },
	isletter: function(k) {
    	var r = /[A-Za-z]/;
    	return (k.length === 1 && r.test(k));
	},
	feedback: function(data) {
    	if (this.callback == null) return;
    	this.callback(data);
	},
    mouseDown:function (evt)  {
    	if (evt.shiftKey) { cw.toggleDir(); return}
    	cw.resetColorMap();
        var rect = this.getBoundingClientRect();
        var cx = evt.clientX - rect.left, cy = evt.clientY - rect.top // adjusted values
        var xposf = (cx/cw.stepx);
        var yposf = (cy/cw.stepy);
        var xpos = Math.floor(cx/cw.stepx);
        var ypos = Math.floor(cy/cw.stepy);
        if(xpos<cw.nx && ypos<cw.ny){
            cw.xpos = xpos;cw.ypos = ypos;
            // prepare hints to be sent via callback, for client/user to display
            var hdata = cw.cwInf[xpos][ypos][0];
            var vdata = cw.cwInf[xpos][ypos][1];
            var fbData = '';
            if (hdata != null) {
                fbData = 'H:'+hdata.hint;
            } else {
            	fbData = 'H:';
            }
			var sep = '<br>';
            if (vdata != null) {
                fbData += sep + 'V:'+vdata.hint;
            } else {
				fbData += sep + 'V:';
            }
			cw.feedback(fbData);
            cw.guessDirection();
            cw.showCursor();
            cw.drawIdxs();
        }
    },
	toggleDir: function() {
    	if (cw.dir == 0) { cw.dir = 1} else { cw.dir = 0;}
        cw.showCursor();
	},
    drawBoard:function() {
    	this.context.save();
		this.context.fillStyle = 'white';
		this.context.fillRect(0,0, this.cwwidth,this.cwheight);
		this.context.fillStyle = 'black';
        this.context.strokeStyle = '#000020';
        this.context.lineWidth = 0.5;

        // Draw board vertical lines
        for (var i = 0 + this.stepx; i < this.cwwidth; i += this.stepx) {
            this.context.beginPath();
            this.context.strokeStyle = "black";
            this.context.moveTo(i, 0);
            this.context.lineTo(i, this.cwheight);
            this.context.stroke();
        }

        for (var i = 0 + this.stepy; i < this.cwheight; i += this.stepy) {
            this.context.beginPath();
            this.context.strokeStyle = "black";
            this.context.moveTo(0, i);
            this.context.lineTo(this.cwwidth, i);
            this.context.stroke();
        }
        this.context.beginPath();
        this.context.moveTo(0, this.cwheight);
        this.context.lineTo(this.cwwidth, this.cwheight);
        this.context.stroke();
        this.drawIdxs();
        //
        for(var j=0;j<this.ny;j++) {
            for(var i=0;i<this.nx;i++)
            {
                if(this.cwMask[i][j]===1) {
                    var xx = i*this.stepx+0.5;
                    var yy = j*this.stepy+0.5;
					this.context.fillStyle = '#00377C';
					this.context.fillRect(xx,yy,this.stepx-0.5,this.stepy-0.5);
                } else if (this.cwMask[i][j] !== 0) {
                    var xx = 0.5+i*this.stepx+this.stepx/2-5;
                    var yy = 0.5+j*this.stepy+this.stepy/2+7;
                    this.fillColor = 'black';
					if(this.colormap[i][j]==0)
						this.fillColor = 'black';
					else
						this.fillColor = 'red';
					this.context.fillStyle = this.fillColor;
                    this.context.font = '14px sans-serif';
                    this.context.fillText(''+this.cwMask[i][j],xx,yy);
                    this.context.font = '10px sans-serif';
                }
            }
        }
        this.context.restore();
    },
	drawCursor: function(color) {
		this.context.beginPath();
		this.context.fillStyle = color;
		this.context.strokeStyle = 'white';
		this.context.moveTo(this.cursorx[0], this.cursory[0]);
		for (var i=1; i < 3; i++) {
			this.context.lineTo(this.cursorx[i], this.cursory[i]);
		}
		this.context.closePath();
		this.context.stroke();
		this.context.fill();
		this.context.restore();
	},
	showCursor: function() {
        if (cw.xpos === undefined || cw.xpos < 0 || cw.ypos === undefined || cw.ypos < 0) { return; }
		if(this.cwMask[cw.xpos][cw.ypos]===1) {
			return;
		}
		if(this.useCursor) {
			this.context.save();
			this.drawCursor('white');
			var x2 = 2, y2 = 2;
			if(this.dir>0)
			{
				this.cursorx[0] = cw.xpos*this.stepx+x2;
				this.cursory[0] = cw.ypos*this.stepy+y2;
				this.cursorx[1] = (cw.xpos+1)*this.stepx-1;
				this.cursory[1] = cw.ypos*this.stepy+y2;
				this.cursorx[2] = cw.xpos*this.stepx+this.stepx/2;
				this.cursory[2] = cw.ypos*this.stepy+this.stepy/4+y2;
			}else
			{
				this.cursorx[0] = cw.xpos*this.stepx+x2;
				this.cursory[0] = cw.ypos*this.stepy+1;
				this.cursorx[1] = cw.xpos*this.stepx+x2;
				this.cursory[1] = (cw.ypos+1)*this.stepy;
				this.cursorx[2] = cw.xpos*this.stepx+this.stepx/4+x2;
				this.cursory[2] = cw.ypos*this.stepy+this.stepy/2;
			}
			this.drawCursor('green');
		}
	},
    drawRect:function(x, y, width, height) {
        this.context.beginPath();
        this.context.moveTo(x, y);
        this.context.lineTo(x + width, y);
        this.context.lineTo(x + width, y + height);
        this.context.lineTo(x, y + height);
        this.context.lineTo(x, y);
        this.context.stroke();
        this.context.closePath();
    },
    drawIdxs:function(){
        this.context.save();
		this.context.fillStyle = 'gray';
		for(var j=0;j<this.ny;j++)
            for(var i=0;i<this.nx;i++)
            {
                wdata = this.cwInf[i][j][0];
                if(wdata==null || wdata.idx == 0)
                    wdata = this.cwInf[i][j][1];
                if(wdata==null || wdata.idx == 0)
                    continue;
                xx = i*this.stepx+2;
                yy = j*this.stepy+10;
                this.context.fillText(''+wdata.idx,xx,yy);
            }
            this.context.restore();
    },
    drawhint:function(context, s, x, y) {

        context.fillText(s,x,y);
    },
    writeWord:function(x, y, dir, word) {
        this.context.font = this.font;
        this.context.fillStyle = this.fillColor;
        var n = word.length;
        var xstart = this.stepx/3;
        var ystart = this.stepy/3;
        for (var i=0;i<n;i++){
            if (dir === 0) {
                this.context.fillText(word[i], x*this.stepx+i*this.stepx+xstart, y*this.stepy+this.stepy/2+4);
            } else {
                this.context.fillText(word[i], x*this.stepx+xstart, y*this.stepy+this.stepy/2+4+i*this.stepy);
            }
        }
    },
    parseData:function(data) {
        cwdata = jQuery(data).find('cw')[0];
        this.name = cwdata.getAttribute('name');
        this.subject = cwdata.getAttribute('subject');
        this.nx = cwdata.getAttribute('cols');
        this.ny = cwdata.getAttribute('rows');
		var rect = this.canvas.getBoundingClientRect();
        this.cwwidth = rect.width;
        this.cwheight = rect.width;
        this.stepx = this.cwwidth/this.nx;
        this.stepy = this.cwheight/this.ny;
        this.initArrays();
        horz = jQuery(data).find('horizontals');
        words = horz.find('word');
        this.parseWords(words, 0);
        verts = jQuery(data).find('verticals');
        words = verts.find('word');
        this.parseWords(words, 1);
        this.setHints();
        this.drawBoard();
    },
    parseWords:function(words, dir) {
        var n = words.length;
        for (var i=0;i<n;i++) {
            word = words[i];
            var value = word.getAttribute('value');
            var xpos = Number(word.getAttribute('xpos'));
            var ypos = Number(word.getAttribute('ypos'));
            var hint = word.childNodes[0].data;
            this.mask(word, dir, hint);
        }
    },
    initArrays:function() {
        this.cwMask = new Array(this.nx);
		this.colormap = new Array(this.nx);
        for(var i=0;i<this.nx;i++) {
            this.cwMask[i] = new Array(this.ny);
			this.colormap[i] = new Array(this.ny);
            for(var j=0;j<this.ny;j++) {
                this.cwMask[i][j] = 1;
				this.colormap[i][j] = 0;
            }
        }
        this.cwInf = new Array(this.nx);
        for(var i=0;i<this.nx;i++) {
            this.cwInf[i] = new Array(this.ny);
            for(var j=0;j<this.ny;j++) {
                this.cwInf[i][j] = new Array(2);
            }
        }
        this.cwnl = new Array(this.nx);
        for(var i=0;i<this.nx;i++) {
            this.cwnl[i] = new Array(this.ny);
            for(var j=0;j<this.ny;j++) {
                this.cwnl[i][j] = 0;
            }
        }
        this.cwv = new Array(50);
        for(var i=0;i<50;i++) {
            this.cwv[i] = new Array(50);
        }
        this.hintcounter = [0,0];
		this.cursorx = new Array(3);
		this.cursory = new Array(3);
    },
	resetColorMap: function() {
		for(var i=0;i<this.nx;i++) {
			for(var j=0;j<this.ny;j++) {
				this.colormap[i][j] = 0;
			}
		}
	},
    mask:function(word, dir, hint) {
        wdata = function(){};
        wdata.value = word.getAttribute('value');
        wdata.xpos = Number(word.getAttribute('xpos'));
        wdata.ypos = Number(word.getAttribute('ypos'));
        wdata.hint = hint;
    	var lg = wdata.value.length;
    	maskValue = 0;
    	if (dir===0) {
            for(var i=0;i<lg;i++) {
                var xp = wdata.xpos+i;
                this.cwMask[xp][wdata.ypos] = maskValue;
            }
    	} else {
            for(var i=0;i<lg;i++) {
                this.cwMask[wdata.xpos][wdata.ypos+i] = maskValue;
            }
    	}
    	// save word info for this position
    	this.cwInf[wdata.xpos][wdata.ypos][dir] = wdata;
    },
    setHints:function() {
        var idxCount = 1;
    	for(var j=0;j<this.ny;j++) {
            for(var i=0;i<this.nx;i++) {
                if(this.cwMask[i][j]===0) {
                    for(var k=0;k<2;k++) { // for each direction
                        wdata = this.cwInf[i][j][k];
                        if(wdata==null)
                                continue;
                        if(this.cwnl[i][j]===0)
                        {
                                this.cwnl[i][j] = idxCount++;
                        }
                        // add word hint
                        this.addHint(wdata,k,this.cwnl[i][j]);
                    }
                }
            }
        }
    },
	getUserWord: function(x, y, lg, dir, c) {
		var word = '', c0 = 0;
		if (dir == 0) {
			for (var i = 0; i < lg; i++) {
				word +=c[x + i][y];
				if (c[x + i][y] === 0) { c0++; }
			}
		} else {
			for (var i = 0; i < lg; i++) {
				word +=c[x][y + i];
				if (c[x][y + i] === 0) { c0++; }
			}
		}
		if (c0 > 1) {
			return "";
		}
		return word;
	},
	evaluate: function() {
    	this.resetColorMap();
		var words = this.cwMask;
		var errCount = 0;
		for (var i = 0; i < 2; i++) {
			for (var j = 0; j < this.ny; j++) {
				if (this.cwv[i][j] == null) {
					continue;
				}
				var x = this.cwv[i][j].xpos;
				var y = this.cwv[i][j].ypos;
				var word = this.cwv[i][j].value;
				var dir = i;
				var userWord = this.getUserWord(x, y, word.length, dir, words);
				if (userWord.length == 0) {
					continue;
				}
				if (word.toLowerCase() != userWord.toLowerCase()) {
					errCount++;
					this.signalError(x, y, dir, word);
				}
			}
		}
		this.drawBoard();
		this.feedback('You have ' + errCount + ' errors');
	},
	solve: function() {
		var words = this.cwMask;
		for (var i = 0; i < 2; i++) {
			for (var j = 0; j < this.ny; j++) {
				if (this.cwv[i][j] == null) {
					continue;
				}
				var x = this.cwv[i][j].xpos;
				var y = this.cwv[i][j].ypos;
				var word = this.cwv[i][j].value;
				var dir = i;
				this.setUserWord(x, y, dir, word, words);
				this.resetSignal(x, y, dir, word);
			}
		}
		this.drawBoard();
	},
	setUserWord: function(x, y, dir, word, c) {
		var lg = word.length;
		for (var i = 0; i < lg; i++) {
			if (dir == 0) {
				this.cwMask[x + i][y] = word.charAt(i);
			} else {
				this.cwMask[x][y + i] = word.charAt(i);
			}
		}
	},
	signalError: function(x, y, dir, word) {
		var lg = word.length;
		for (var i = 0; i < lg; i++) {
			if (dir == 0) {
				this.colormap[x + i][y] = 1;
			} else {
				this.colormap[x][y + i] = 1;
			}
		}
	},
	resetSignal: function(x, y, dir, word) {
		var lg = word.length;
		for (var i = 0; i < lg; i++) {
			if (dir == 0) {
				this.colormap[x + i][y] = 0;
			} else {
				this.colormap[x][y + i] = 0;
			}
		}
	},
	addHint:function(hint, dir, idx) {
    	hint.idx = idx;
    	this.cwv[dir][this.hintcounter[dir]++] = hint;
    },
    guessDirection(){
        if(cw.xpos<cw.nx && cw.ypos<cw.ny){
            cw.dir=-1;
            wdata = cw.cwInf[cw.xpos][cw.ypos][0];
            if(wdata!=null){
                cw.dir=0;
            }
            wdata = cw.cwInf[cw.xpos][cw.ypos][1];
            if(wdata!=null){
                 if(cw.dir==-1) {
                    cw.dir=1;
                 }
            }
        }
    },
    load:function(qs, cw) {
        jQuery.ajax({
        url: qs,
        success: function(data) {
           cw.parseData(data);
        }
     });
    }
};

var cw = null;
// expects:
// canvasId: id of html canvas element,
// dataPath: the path to the crossword data,
// callback: a callback function to receive output such as hints and errors
function crossword(canvasId, dataPath, callback) {
	var canvas = document.getElementById(canvasId);
	cw = new Crossword(canvas, callback);
	cw.load(dataPath, cw);
}

