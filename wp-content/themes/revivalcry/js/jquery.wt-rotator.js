/**
 * jQuery Banner Rotator 
 * Copyright (c) 2011 Allan Ma (http://codecanyon.net/user/webtako)
 * Version: 1.6.2 (09/08/2011)
 */
;(function(jQuery) {
	jQuery.fn.wtRotator = function(params) {
		var INSIDE = "inside";
		var OUTSIDE = "outside";
		var PREV = 0;
		var NEXT = 1;
		var ALIGN = {"TL":0, "TC":1, "TR":2, "BL":3, "BC":4, "BR":5, "LT":6, "LC":7, "LB":8, "RT":9, "RC":10,"RB":11};
		var ei = 0;
		var EFFECTS = {			
			"block.top":ei++,
			"block.right":ei++,
			"block.bottom":ei++,
			"block.left":ei++,
			"block.drop":ei++,
			"diag.fade":ei++,
			"diag.exp":ei++,
			"rev.diag.fade":ei++,
			"rev.diag.exp":ei++,
			"block.fade":ei++,
			"block.exp":ei++,
			"block.top.zz":ei++,
			"block.bottom.zz":ei++,
			"block.left.zz":ei++,
			"block.right.zz":ei++,
			"spiral.in":ei++,
			"spiral.out":ei++,
			"vert.tl":ei++,
			"vert.tr":ei++,
			"vert.bl":ei++,
			"vert.br":ei++,
			"fade.left":ei++,
			"fade.right":ei++,	
			"alt.left":ei++,
			"alt.right":ei++,
			"blinds.left":ei++,
			"blinds.right":ei++,
			"vert.random.fade":ei++,
			"horz.tl":ei++,
			"horz.tr":ei++,
			"horz.bl":ei++,
			"horz.br":ei++,
			"fade.top":ei++,
			"fade.bottom":ei++,
			"alt.top":ei++,
			"alt.bottom":ei++,
			"blinds.top":ei++, 
			"blinds.bottom":ei++,
			"horz.random.fade":ei++,
			"none":ei++,
			"fade":ei++,
			"h.slide":ei++,
			"v.slide":ei++,
			"random":ei++
		};
		var TEXT_EFFECTS = {"fade":0, "down":1, "right":2, "up":3, "left":4, "none":5}
		
		var LIMIT = 250;
		var BLOCK_SIZE = 75;
		var STRIPE_SIZE = 50;
		var DEFAULT_DELAY = 5000;
		var DURATION = 800;
		var ANIMATE_SPEED = 500;
		var TOOLTIP_DELAY = 600;
		var SCROLL_RATE = 4;							
		var UPDATE_TEXT = "updatetext";
		var UPDATE_LIST = "updatelist";
		var MSIE7_BELOW = (jQuery.browser.msie && parseInt(jQuery.browser.version) <= 7);
		//Vertical Stripes
		function VertStripes(rotator, areaWidth, areaHeight, stripeSize, bgColor, duration, delay) {
			var jQuerystripes;
			var jQueryarr;
			var total;
			var intervalId = null;
			
			//init stripes
			var init = function() {
				total = Math.ceil(areaWidth/stripeSize);
				if (total > LIMIT) {
					stripeSize = Math.ceil(areaWidth/LIMIT);
					total = Math.ceil(areaWidth/stripeSize);
				}
				var divs = "";
				for (var i = 0; i < total; i++) {
					divs += "<div class='vpiece' id='" + i + "'></div>";
				}					
				rotator.addToScreen(divs);
				
				jQuerystripes = rotator.jQueryel.find("div.vpiece");
				jQueryarr = new Array(total);
				jQuerystripes.each(
					function(n) {						
						jQuery(this).css({left:(n * stripeSize), height: areaHeight});
						jQueryarr[n] = jQuery(this);
					}
				);
			}

			//clear animation
			this.clear = function() {
				clearInterval(intervalId);
				jQuerystripes.stop(true).css({"z-index":2, opacity:0});
			}

			//display content
			this.displayContent = function(jQueryimg, effect) {
				setPieces(jQueryimg, effect);
				if (effect == EFFECTS["vert.random.fade"]) {
					animateRandom(jQueryimg);
				}
				else {
					animate(jQueryimg, effect);
				}
			}			
			
			//set image stripes
			var setPieces = function(jQueryimg, effect) {
				switch (effect) {
					case EFFECTS["vert.tl"]:
					case EFFECTS["vert.tr"]:
						setVertPieces(jQueryimg, -areaHeight, 1, stripeSize, false);
						break;
					case EFFECTS["vert.bl"]:
					case EFFECTS["vert.br"]:
						setVertPieces(jQueryimg, areaHeight, 1, stripeSize, false);
						break;
					case EFFECTS["alt.left"]:
					case EFFECTS["alt.right"]:
						setVertPieces(jQueryimg, 0, 1, stripeSize, true);
						break;
					case EFFECTS["blinds.left"]:
					case EFFECTS["blinds.right"]:
						setVertPieces(jQueryimg, 0, 1, 0, false);
						break;
					default:
						setVertPieces(jQueryimg, 0, 0, stripeSize, false);
				}
			}
			
			//set vertical stripes
			var setVertPieces = function(jQueryimg, topPos, opacity, width, alt) {
				var imgSrc = jQueryimg.attr("src");
				var tOffset = 0;
				var lOffset = 0;
				if (rotator.autoCenter()) {
					tOffset = (areaHeight - jQueryimg.height())/2;
					lOffset = (areaWidth - jQueryimg.width())/2;
				}
				jQuerystripes.each(
					function(n) {
						var xPos =  ((-n * stripeSize) + lOffset);
						if (alt) {
							topPos = (n % 2) == 0 ? -areaHeight: areaHeight;
						}
						jQuery(this).css({background:bgColor + " url('"+ imgSrc +"') no-repeat", backgroundPosition:xPos + "px " + tOffset + "px", opacity:opacity, top:topPos, width:width, "z-index":3});
					});
			}
			
			//animate stripes			
			var animate = function(jQueryimg, effect) {
				var start, end, incr, limit;
				switch (effect) {
					case EFFECTS["vert.tl"]:   case EFFECTS["vert.bl"]: 
					case EFFECTS["fade.left"]: case EFFECTS["blinds.left"]: 
					case EFFECTS["alt.left"]:
						start = 0;
						end = total - 1;
						incr = 1;
						break;
					default:
						start = total - 1;
						end = 0;
						incr = -1;
				}
				
				intervalId = setInterval(
					function() {
						jQuerystripes.eq(start).animate({top:0, opacity:1, width:stripeSize}, duration, rotator.easing(),
							function() {
								if (jQuery(this).attr("id") == end) {
									rotator.setComplete(jQueryimg);
								}
							}
						);
						if (start == end) {
							clearInterval(intervalId);
						}
						start += incr;
					}, delay);
			}
			
			//animate random fade 
			var animateRandom = function(jQueryimg) {		
				shuffleArray(jQueryarr);
				var i = 0;
				var count = 0;
				intervalId = setInterval(
					function() {
						jQueryarr[i++].animate({opacity:1}, duration, rotator.easing(),
								function() {
									if (++count == total) {
										rotator.setComplete(jQueryimg);
									}
								});
						if (i == total) {
							clearInterval(intervalId);
						}
					}, delay);
			}
			
			init();
		}
		
		//Horizontal Stripes
		function HorzStripes(rotator, areaWidth, areaHeight, stripeSize, bgColor, duration, delay) {
			var jQuerystripes;
			var jQueryarr;
			var total;
			var intervalId = null;
			
			//init stripes
			var init = function() {			
				total = Math.ceil(areaHeight/stripeSize);
				if (total > LIMIT) {
					stripeSize = Math.ceil(areaHeight/LIMIT);
					total = Math.ceil(areaHeight/stripeSize);
				}
				var divs = "";
				for (var j = 0; j < total; j++) {
					divs += "<div class='hpiece' id='" + j + "'><!-- --></div>";
				}				
				rotator.addToScreen(divs);
				
				jQuerystripes = rotator.jQueryel.find("div.hpiece");
				jQueryarr = new Array(total);
				jQuerystripes.each(
					function(n) {
						jQuery(this).css({top:(n * stripeSize), width: areaWidth});
						jQueryarr[n] = jQuery(this);
					}							 
				);
			}

			//clear animation
			this.clear = function() {
				clearInterval(intervalId);
				jQuerystripes.stop(true).css({"z-index":2, opacity:0});
			}

			//display content
			this.displayContent = function(jQueryimg, effect) {
				setPieces(jQueryimg, effect);
				if (effect == EFFECTS["horz.random.fade"]) {
					animateRandom(jQueryimg);
				}
				else {
					animate(jQueryimg, effect);
				}
			}			
			
			//set image stripes
			var setPieces = function(jQueryimg, effect) {
				switch (effect) {
					case EFFECTS["horz.tr"]:
					case EFFECTS["horz.br"]:
						setHorzPieces(jQueryimg, areaWidth, 1, stripeSize, false);
						break;
					case EFFECTS["horz.tl"]:
					case EFFECTS["horz.bl"]:
						setHorzPieces(jQueryimg, -areaWidth, 1, stripeSize, false);
						break;
					case EFFECTS["alt.top"]:
					case EFFECTS["alt.bottom"]:
						setHorzPieces(jQueryimg, 0, 1, stripeSize, true);
						break;
					case EFFECTS["blinds.top"]:
					case EFFECTS["blinds.bottom"]:
						setHorzPieces(jQueryimg, 0, 1, 0, false);
						break;
					default:
						setHorzPieces(jQueryimg, 0, 0, stripeSize, false);
				}
			}
			
			//set horizontal stripes
			var setHorzPieces = function(jQueryimg, leftPos, opacity, height, alt) {
				var imgSrc = jQueryimg.attr("src");
				var tOffset = 0;
				var lOffset = 0;
				if (rotator.autoCenter()) {
					tOffset = (areaHeight - jQueryimg.height())/2;
					lOffset = (areaWidth - jQueryimg.width())/2;
				}
				jQuerystripes.each(
					function(n) {
						var yPos = ((-n * stripeSize) + tOffset);
						if (alt) {
							leftPos = (n % 2) == 0 ? -areaWidth: areaWidth;
						}
						jQuery(this).css({background:bgColor + " url('"+ imgSrc +"') no-repeat", backgroundPosition:lOffset + "px " + yPos + "px", opacity:opacity, left:leftPos, height:height, "z-index":3});  
					});
			}
			
			//animate stripes			
			var animate = function(jQueryimg, effect) {
				var start, end, incr;
				switch (effect) {
					case EFFECTS["horz.tl"]:  case EFFECTS["horz.tr"]: 
					case EFFECTS["fade.top"]: case EFFECTS["blinds.top"]: 
					case EFFECTS["alt.top"]:
						start = 0;
						end = total - 1;
						incr = 1;
						break;
					default:
						start = total - 1;
						end = 0;
						incr = -1;
				}
				
				intervalId = setInterval(
					function() {
						jQuerystripes.eq(start).animate({left:0, opacity:1, height:stripeSize}, duration, rotator.easing(),
							function() {
								if (jQuery(this).attr("id") == end) {
									rotator.setComplete(jQueryimg);
								}
							}
						);
						if (start == end) {
							clearInterval(intervalId);
						}
						start += incr;
					}, delay);
			}
			
			//animate random fade 
			var animateRandom = function(jQueryimg) {		
				shuffleArray(jQueryarr);
				var i = 0;
				var count = 0;
				intervalId = setInterval(
					function() {
						jQueryarr[i++].animate({opacity:1}, duration, rotator.easing(),
								function() {
									if (++count == total) {
										rotator.setComplete(jQueryimg);
									}
								});
						if (i == total) {
							clearInterval(intervalId);
						}
					}, delay);
			}
			
			init();
		}
		
		//class Blocks
		function Blocks(rotator, areaWidth, areaHeight, blockSize, bgColor, duration, delay) {
			var jQueryblockArr;
			var jQueryblocks;
			var jQueryarr;
			var numRows;
			var numCols;
			var total;
			var intervalId;
			
			//init blocks
			var init = function() {
				numRows = Math.ceil(areaHeight/blockSize);
				numCols = Math.ceil(areaWidth/blockSize);
				total = numRows * numCols;
				if (total > LIMIT) {
					blockSize = Math.ceil(Math.sqrt((areaHeight * areaWidth)/LIMIT));
					numRows = Math.ceil(areaHeight/blockSize);
					numCols = Math.ceil(areaWidth/blockSize);
					total = numRows * numCols;
				}
				
				var divs = "";
				for (var i = 0; i < numRows; i++) {					
					for (var j = 0; j < numCols; j++) {
						divs += "<div class='block' id='" + i + "-" + j + "'></div>";
					}
				}
				rotator.addToScreen(divs);
				jQueryblocks = rotator.jQueryel.find("div.block");
				jQueryblocks.data({tlId:"0-0", trId:"0-"+(numCols - 1), blId:(numRows - 1)+"-0", brId:(numRows - 1)+"-"+(numCols - 1)});
				
				var k = 0;
				jQueryarr = new Array(total);
				jQueryblockArr = new Array(numRows);
				for (var i = 0; i < numRows; i++) {
					jQueryblockArr[i] = new Array(numCols);
					for (var j = 0; j < numCols; j++) {
						jQueryblockArr[i][j] = jQueryarr[k++] = jQueryblocks.filter("#" + (i + "-" + j)).data("top", i * blockSize);
					}
				}				
			}
			
			//clear blocks
			this.clear = function() {
				clearInterval(intervalId);
				jQueryblocks.stop(true).css({"z-index":2, opacity:0});
			}
			
			//display content
			this.displayContent = function(jQueryimg, effect) {
				switch (effect) {
					case EFFECTS["diag.fade"]:
						setBlocks(jQueryimg, 0, blockSize, 0);
						diagAnimate(jQueryimg, {opacity:1}, false);
						break;
					case EFFECTS["diag.exp"]:
						setBlocks(jQueryimg, 0, 0, 0);
						diagAnimate(jQueryimg, {opacity:1, width:blockSize, height:blockSize}, false);
						break;
					case EFFECTS["rev.diag.fade"]:
						setBlocks(jQueryimg, 0, blockSize, 0);
						diagAnimate(jQueryimg, {opacity:1}, true);
						break;
					case EFFECTS["rev.diag.exp"]:
						setBlocks(jQueryimg, 0, 0, 0);
						diagAnimate(jQueryimg, {opacity:1, width:blockSize, height:blockSize}, true);
						break;
					case EFFECTS["block.fade"]:
						setBlocks(jQueryimg, 0, blockSize, 0);
						randomAnimate(jQueryimg);
						break;
					case EFFECTS["block.exp"]:
						setBlocks(jQueryimg, 1, 0, 0);
						randomAnimate(jQueryimg);
						break; 
					case EFFECTS["block.drop"]:
						setBlocks(jQueryimg, 1, blockSize, -(numRows * blockSize));
						randomAnimate(jQueryimg);
						break;
					case EFFECTS["block.top.zz"]: 
					case EFFECTS["block.bottom.zz"]:					
						setBlocks(jQueryimg, 0, blockSize, 0);
						horzZigZag(jQueryimg, effect);
						break;
					case EFFECTS["block.left.zz"]: 
					case EFFECTS["block.right.zz"]:
						setBlocks(jQueryimg, 0, blockSize, 0);
						vertZigZag(jQueryimg, effect);
						break;
					case EFFECTS["spiral.in"]:
						setBlocks(jQueryimg, 0, blockSize, 0);
						spiral(jQueryimg, false);
						break;
					case EFFECTS["spiral.out"]:
						setBlocks(jQueryimg, 0, blockSize, 0);
						spiral(jQueryimg, true);
						break;
					default:
						setBlocks(jQueryimg, 1, 0, 0);
						dirAnimate(jQueryimg, effect);
				}
			}
			
			//set blocks 
			var setBlocks = function(jQueryimg, opacity, size, tPos) {
				var tOffset = 0;
				var lOffset = 0;
				if (rotator.autoCenter()) {
					tOffset = (areaHeight - jQueryimg.height())/2;
					lOffset = (areaWidth - jQueryimg.width())/2;
				}
				var imgSrc = jQueryimg.attr("src");
				for (var i = 0; i < numRows; i++) {							
					for (var j = 0; j < numCols; j++) {
						var tVal = ((-i * blockSize) + tOffset);
						var lVal = ((-j * blockSize) + lOffset);
						jQueryblockArr[i][j].css({background:bgColor + " url('"+ imgSrc +"') no-repeat", backgroundPosition:lVal + "px " + tVal + "px", opacity:opacity, top:(i * blockSize) + tPos, left:(j * blockSize), width:size, height:size, "z-index":3});
					}					
				}
			}
			
			//diagonal effect
			var diagAnimate = function(jQueryimg, props, rev) {
				var jQueryarray = new Array(total);
				var start, end, incr, lastId;
				var diagSpan = (numRows - 1) + (numCols - 1);
				if (rev) {				
					start = diagSpan;
					end = -1;
					incr = -1;
					lastId = jQueryblocks.data("tlId");
				}
				else {
					start = 0;
					end = diagSpan + 1;
					incr = 1;
					lastId = jQueryblocks.data("brId");
				}
				
				var count = 0;
				while (start != end) {
					i = Math.min(numRows - 1, start);
					while(i >= 0) {			
						j = Math.abs(i - start);
						if (j >= numCols) {
							break;
						}
						jQueryarray[count++] = jQueryblockArr[i][j];
						i--;
					}
					start+=incr;
				}
				
				count = 0;
				intervalId = setInterval(
					function() {
						jQueryarray[count++].animate(props, duration, rotator.easing(),
								function() {
									if (jQuery(this).attr("id") == lastId) {
										rotator.setComplete(jQueryimg);
									}
								});
						if (count == total) {
							clearInterval(intervalId);
						}			
					}, delay);
			}

			//vertical zig zag effect
			var vertZigZag = function(jQueryimg, effect) {
				var fwd = true;
				var i = 0, j, incr, lastId;
				if (effect == EFFECTS["block.left.zz"]) {
					lastId = (numCols%2 == 0) ? jQueryblocks.data("trId") : jQueryblocks.data("brId");
					j = 0;
					incr = 1;
				}
				else {
					lastId = (numCols%2 == 0) ? jQueryblocks.data("tlId") : jQueryblocks.data("blId");
					j = numCols - 1;
					incr = -1;
				}
				
				intervalId = setInterval(
					function() {
						jQueryblockArr[i][j].animate({opacity:1}, duration, rotator.easing(),
								function() {
									if (jQuery(this).attr("id") == lastId) {
										rotator.setComplete(jQueryimg);
									}});
						
						if (jQueryblockArr[i][j].attr("id") == lastId) {
							clearInterval(intervalId);
						}
						
						(fwd ? i++ : i--);
						if (i == numRows || i < 0) {
							fwd = !fwd;
							i = (fwd ? 0 : numRows - 1);
							j+=incr;
						}						
					}, delay);
			}
			
			//horizontal zig zag effect
			var horzZigZag = function(jQueryimg, effect) {
				var fwd = true;
				var i, j = 0, incr, lastId;
				if (effect == EFFECTS["block.top.zz"]) {
					lastId = (numRows%2 == 0) ? jQueryblocks.data("blId") : jQueryblocks.data("brId");
					i = 0;
					incr = 1;
				}
				else {
					lastId = (numRows%2 == 0) ? jQueryblocks.data("tlId") : jQueryblocks.data("trId");
					i = numRows - 1;
					incr = -1;
				}
				
				intervalId = setInterval(
					function() {
						jQueryblockArr[i][j].animate({opacity:1}, duration, rotator.easing(),
								function() {
									if (jQuery(this).attr("id") == lastId) {
										rotator.setComplete(jQueryimg);
									}});
						
						if (jQueryblockArr[i][j].attr("id") == lastId) {
							clearInterval(intervalId);
						}
						
						(fwd ? j++ : j--);
						if (j == numCols || j < 0) {
							fwd = !fwd;
							j = (fwd ? 0 : numCols - 1);
							i+=incr;
						}						
					}, delay);
			}
			
			//vertical direction effect
			var dirAnimate = function(jQueryimg, effect) {
				var jQueryarray = new Array(total);
				var lastId;
				var count = 0;
				switch (effect) {
					case EFFECTS["block.left"]:
						lastId = jQueryblocks.data("brId");
						for (var j = 0; j < numCols; j++) {
							for (var i = 0; i < numRows; i++) {
								jQueryarray[count++] = jQueryblockArr[i][j];
							}
						}
						break;
					case EFFECTS["block.right"]:
						lastId = jQueryblocks.data("blId");
						for (var j = numCols - 1; j >= 0; j--) {
							for (var i = 0; i < numRows; i++) {
								jQueryarray[count++] = jQueryblockArr[i][j];
							}
						}					
						break;
					case EFFECTS["block.top"]:
						lastId = jQueryblocks.data("brId");
						for (var i = 0; i < numRows; i++) {
							for (var j = 0; j < numCols; j++) {
								jQueryarray[count++] = jQueryblockArr[i][j];
							}
						}					
						break;
					default:
						lastId = jQueryblocks.data("trId");
						for (var i = numRows - 1; i >= 0; i--) {
							for (var j = 0; j < numCols; j++) {
								jQueryarray[count++] = jQueryblockArr[i][j];
							}
						}
				}
				count = 0;
				intervalId = setInterval(
					function() {
						jQueryarray[count++].animate({width:blockSize, height:blockSize}, duration, rotator.easing(),
								function() {
									if (jQuery(this).attr("id") == lastId) {
										rotator.setComplete(jQueryimg);
									}
								});
						if (count == total) {
							clearInterval(intervalId);
						}
					}, delay);
			}
			
			//random block effect
			var randomAnimate = function(jQueryimg) {
				shuffleArray(jQueryarr);
				var i = 0;
				count = 0;
				intervalId = setInterval(
					function() {
						jQueryarr[i].animate({top:jQueryarr[i].data("top"), width:blockSize, height:blockSize, opacity:1}, duration, rotator.easing(),
								function() {
									if (++count == total) {
										rotator.setComplete(jQueryimg);
									}
								});
						i++;
						if (i == total) {
							clearInterval(intervalId);
						}
					}, delay);
			}
			
			//spiral effect
			var spiral = function(jQueryimg, spiralOut) {			
				var i = 0, j = 0;
				var rowCount = numRows - 1;
				var colCount = numCols - 1;
				var dir = 0;
				var limit = colCount;
				var jQueryarray = new Array();
				while (rowCount >= 0 && colCount >=0) {
					var count = 0; 
					while(true) { 
						jQueryarray[jQueryarray.length] = jQueryblockArr[i][j];
						if ((++count) > limit) {
							break;
						}
						switch(dir) {
							case 0:
								j++;
								break;
							case 1:
								i++;
								break;
							case 2:
								j--;
								break;
							case 3:
								i--;
						}
   					} 
					switch(dir) {
						case 0:
							dir = 1;
							limit = (--rowCount);
							i++;
							break;
						case 1:
							dir = 2;
							limit = (--colCount);
							j--;
							break;
						case 2:
							dir = 3;
							limit = (--rowCount);
							i--;
							break;
						case 3:
							dir = 0;
							limit = (--colCount);
							j++;
					}
				}
				if (jQueryarray.length > 0) {
					if (spiralOut) {
						jQueryarray.reverse();
					}
					var end = jQueryarray.length - 1;
					var lastId = jQueryarray[end].attr("id");
					var k = 0;
					intervalId = setInterval(
						function() {
							jQueryarray[k].animate({opacity:1}, duration, rotator.easing(),
								function() {
									if (jQuery(this).attr("id") == lastId) {
										rotator.setComplete(jQueryimg);
									}
								});
							if (k == end) {
								clearInterval(intervalId);
							}	
							k++;
						}, delay);
				}
			}
			
			init();
		}
		
		//class Rotator
		function Rotator(jQueryobj, opts) {
			//set options
			var screenWidth =  	getPosNumber(opts.width, 825);
			var screenHeight = 	getPosNumber(opts.height, 300);
			var margin = 		getNonNegNumber(opts.button_margin, 4);
			var globalEffect = 	opts.transition.toLowerCase();
			var duration =   	getPosNumber(opts.transition_speed, DURATION);
			var globalDelay = 	getPosNumber(opts.delay, DEFAULT_DELAY);
			var rotate = 		opts.auto_start;
			var cpPos =			opts.cpanel_position.toLowerCase();
			var cpAlign = 		opts.cpanel_align.toUpperCase();			
			var thumbWidth =	getPosNumber(opts.thumb_width, 24);
			var thumbHeight = 	getPosNumber(opts.thumb_height, 24);
			var buttonWidth =  	getPosNumber(opts.button_width, 24);
			var buttonHeight =	getPosNumber(opts.button_height, 24);
			var displayThumbImg = opts.display_thumbimg;
			var displayThumbs = opts.display_thumbs;
			var displaySideBtns = opts.display_side_buttons;
			var displayDBtns = 	opts.display_dbuttons;
			var displayPlayBtn =  opts.display_playbutton;
			var displayNumbers = opts.display_numbers;			
			var displayTimer =	opts.display_timer;
			var cpMouseover = 	opts.cpanel_mouseover;
			var textMousover = 	opts.text_mouseover;
			var pauseMouseover = opts.mouseover_pause;			
			var tipType = 		opts.tooltip_type.toLowerCase();
			var textEffect = 	opts.text_effect.toLowerCase();
			var textSync =		opts.text_sync;
			var playOnce =		opts.play_once;
			var autoCenter =	opts.auto_center;
			var easing =		opts.easing;
			
			var numItems;
			var currIndex;
			var prevIndex;
			var delay;
			var vStripes;
			var hStripes;
			var blocks;
			var timerId;
			var blockEffect;
			var hStripeEffect;
			var vStripeEffect;
			var dir;
			var cpVertical;
			var jQueryrotator;
			var jQueryscreen;
			var jQuerystrip;
			var jQuerymainLink;
			var jQuerytextBox;
			var jQuerypreloader;
			var jQuerycpWrapper;
			var jQuerycpanel;
			var jQuerythumbPanel;
			var jQuerylist;						
			var jQuerythumbs;
			var jQuerybuttonPanel;
			var jQueryplayBtn;
			var jQuerysPrev;
			var jQuerysNext;
			var jQuerytimer;
			var jQuerytooltip;
			var jQueryitems;
			var jQueryinnerText;
			this.jQueryel = jQueryobj;
			
			//init rotator
			this.init = function() {
				jQueryrotator = jQueryobj.find(".wt-rotator");
				jQueryscreen = jQueryrotator.find("div.screen");
				jQuerycpanel = jQueryrotator.find("div.c-panel");
				jQuerybuttonPanel = jQuerycpanel.find("div.buttons");
				jQuerythumbPanel = jQuerycpanel.find("div.thumbnails");
				jQuerylist = jQuerythumbPanel.find(">ul")
				jQuerythumbs 	= jQuerylist.find(">li");
				timerId = null;
				currIndex = 0;
				prevIndex = -1;
				numItems = jQuerythumbs.size();
				jQueryitems = new Array(numItems);
				blockEffect = hStripeEffect = vStripeEffect = false;
				checkEffect(EFFECTS[globalEffect]);
				cpVertical = ALIGN[cpAlign] >= ALIGN["LT"] ? true : false;
				if (displaySideBtns) {
					displayDBtns = false;
				}
				if (displayThumbImg) {
					displayNumbers = false;
				}
				
				jQueryrotator.css({width:screenWidth, height:screenHeight});
				//init components
				initScreen();				
				initButtons();
				initItems();
				initCPanel();
				initTimerBar();
				
				if (textMousover) {
					jQueryrotator.hover(displayText, hideText);
				}
				else {
					jQueryrotator.bind(UPDATE_TEXT, updateText);
				}
				
				//init transition components
				var bgColor = jQueryscreen.css("background-color");
				if (vStripeEffect) {
					vStripes = new VertStripes(this, screenWidth, screenHeight, getPosNumber(opts.vert_size, STRIPE_SIZE), bgColor, duration, getPosNumber(opts.vstripe_delay, 75));
				}
				if (hStripeEffect) {
					hStripes = new HorzStripes(this, screenWidth, screenHeight, getPosNumber(opts.horz_size, STRIPE_SIZE), bgColor, duration, getPosNumber(opts.hstripe_delay, 75));
				}
				if (blockEffect) {
					blocks = new Blocks(this, screenWidth, screenHeight, getPosNumber(opts.block_size, BLOCK_SIZE), bgColor, duration, getPosNumber(opts.block_delay, 25));
				}
				
				//init image loading
				loadImg(0);
				
				//display initial image
				loadContent(currIndex);
			}
			
			//set complete
			this.setComplete = function(jQueryimg) {
				showContent(jQueryimg);
			}
			
			//add to screen
			this.addToScreen = function(content) {
				jQuerymainLink.append(content);
			}
			
			//get auto center
			this.autoCenter = function() {
				return autoCenter;
			}
			
			//get easing
			this.easing = function() {
				return easing;
			}
			
			//init screen
			var initScreen = function() {
				var content =  "<div class='desc'><div class='inner-bg'></div><div class='inner-text'></div></div>\
								<div class='preloader'></div>\
								<div id='timer'></div>";
				jQueryscreen.append(content);
				jQuerytextBox 	= jQueryscreen.find("div.desc");
			 	jQuerypreloader 	= jQueryscreen.find("div.preloader");
				jQueryscreen.css({width:screenWidth, height:screenHeight});
				jQueryinnerText = jQuerytextBox.find("div.inner-text");
				
				jQuerystrip = jQuery("<div id='strip'></div>");
				if (globalEffect == "h.slide") {
					jQueryscreen.append(jQuerystrip);
					jQuerystrip.css({width:2*screenWidth, height:screenHeight});
					jQuerythumbs.removeAttr("effect");
				}
				else if (globalEffect == "v.slide"){
					jQueryscreen.append(jQuerystrip);
					jQuerystrip.css({width:screenWidth, height:2*screenHeight});
					jQuerythumbs.removeAttr("effect");
				}
				else {
					jQueryscreen.append("<a href='#'></a>");
					jQuerymainLink = jQueryscreen.find(">a:first");
				}
			}
			
			//init control panel
			var initCPanel = function() {	
				if (displayThumbs || displayDBtns || displayPlayBtn) {
					if (cpPos == INSIDE) {
						switch (ALIGN[cpAlign]) {
							case ALIGN["BL"]:								
								setHPanel("left");
								setInsideHP("bottom");
								break;
							case ALIGN["BC"]:
								setHPanel("center");
								setInsideHP("bottom");								
								break;
							case ALIGN["BR"]:
								setHPanel("right");
								setInsideHP("bottom");								
								break;
							case ALIGN["TL"]:								
								setHPanel("left");
								setInsideHP("top");
								break;
							case ALIGN["TC"]:								
								setHPanel("center");
								setInsideHP("top");
								break;
							case ALIGN["TR"]:								
								setHPanel("right");
								setInsideHP("top");
								break;							
							case ALIGN["LT"]:
								setVPanel("top");
								setInsideVP("left");								
								break;
							case ALIGN["LC"]:
								setVPanel("center");							
								setInsideVP("left");
								break;
							case ALIGN["LB"]:
								setVPanel("bottom");							
								setInsideVP("left");
								break;
							case ALIGN["RT"]:								
								setVPanel("top");
								setInsideVP("right");
								break;
							case ALIGN["RC"]:								
								setVPanel("center");
								setInsideVP("right");
								break;
							case ALIGN["RB"]:								
								setVPanel("bottom");
								setInsideVP("right");
								break;
						}
						
						if (cpMouseover) {
							jQueryrotator.hover(displayCPanel, hideCPanel);
						}
					}
					else {
						switch (ALIGN[cpAlign]) {
							case ALIGN["BL"]:
								setHPanel("left");
								setOutsideHP(false);								
								break;
							case ALIGN["BC"]:
								setHPanel("center");
								setOutsideHP(false);								
								break;
							case ALIGN["BR"]:
								setHPanel("right");
								setOutsideHP(false);							
								break;
							case ALIGN["TL"]:
								setHPanel("left");
								setOutsideHP(true);							
								break;
							case ALIGN["TC"]:							
								setHPanel("center");
								setOutsideHP(true);
								break;
							case ALIGN["TR"]:
								setHPanel("right");
								setOutsideHP(true);								
								break;							
							case ALIGN["LT"]:
								setVPanel("top");
								setOutsideVP(true);								
								break;
							case ALIGN["LC"]:
								setVPanel("center");
								setOutsideVP(true);
								break;
							case ALIGN["LB"]:								
								setVPanel("bottom");
								setOutsideVP(true);
								break;
							case ALIGN["RT"]:								
								setVPanel("top");
								setOutsideVP(false);
								break;
							case ALIGN["RC"]:								
								setVPanel("center");
								setOutsideVP(false);
								break;
							case ALIGN["RB"]:
								setVPanel("bottom");
								setOutsideVP(false);
								break;
						}
					}
					jQuerycpanel.css("visibility", "visible").click(preventDefault);
				}
			}
			
			//set control panel attributes
			var setHPanel = function(align) {
				jQuerycpanel.css({"margin-top":margin, "margin-bottom":margin, height:Math.max(jQuerythumbPanel.outerHeight(true), jQuerybuttonPanel.outerHeight(true))});
				var alignPos;
				if (align == "center") {
					alignPos = Math.round((screenWidth - jQuerycpanel.width() - margin)/2);
				}
				else if (align == "left") {
					alignPos = margin;
				}
				else {
					alignPos = screenWidth - jQuerycpanel.width();
				}
				jQuerycpanel.css("left", alignPos);
			}
			
			var setVPanel = function(align) {
				jQuerycpanel.css({"margin-left":margin, "margin-right":margin, width:Math.max(jQuerythumbPanel.outerWidth(true), jQuerybuttonPanel.outerWidth(true))});
				var alignPos;
				if (align == "center") {
					alignPos = Math.round((screenHeight - jQuerycpanel.height() - margin)/2);
				}
				else if (align == "top") {
					alignPos = margin;
				}
				else {
					alignPos = screenHeight - jQuerycpanel.height();
				}
				jQuerycpanel.css("top", alignPos);
			}
			
			var setInsideHP = function(align) {
				var offset, alignPos;
				if (align == "top") {
					alignPos = 0;
					offset = -jQuerycpanel.outerHeight(true);
				}
				else {
					alignPos = screenHeight - jQuerycpanel.outerHeight(true);
					offset = screenHeight;
				}
				jQuerycpanel.data({offset:offset, pos:alignPos}).css({top: (cpMouseover ? offset : alignPos)});
			}
			
			var setInsideVP = function(align) {
				var offset, alignPos;
				if (align == "left") {
					alignPos = 0;
					offset = -jQuerycpanel.outerWidth(true);
				}
				else {
					alignPos = screenWidth - jQuerycpanel.outerWidth(true);
					offset = screenWidth;
				}
				jQuerycpanel.data({offset:offset, pos:alignPos}).css({left:(cpMouseover ? offset : alignPos)});
			}
			
			var setOutsideHP = function(top) {
				jQuerycpanel.wrap("<div class='outer-hp'></div>");
				jQuerycpWrapper = jQueryrotator.find(".outer-hp");
				jQuerycpWrapper.height(jQuerycpanel.outerHeight(true));
							
				if (top) {
					jQuerycpWrapper.css({"border-top":"none", top:0});
					jQueryscreen.css("top", jQuerycpWrapper.outerHeight());
				}
				else {
					jQuerycpWrapper.css({"border-bottom":"none", top:screenHeight});
					jQueryscreen.css("top", 0);
				}
				jQueryrotator.css({height:screenHeight + jQuerycpWrapper.outerHeight()});
			}
			
			var setOutsideVP = function(left) {
				jQuerycpanel.wrap("<div class='outer-vp'></div>");
				jQuerycpWrapper = jQueryrotator.find(".outer-vp");
				jQuerycpWrapper.width(jQuerycpanel.outerWidth(true));
				
				if (left) {
					jQuerycpWrapper.css({"border-left":"none", left:0});
					jQueryscreen.css("left", jQuerycpWrapper.outerWidth());
				}
				else {
					jQuerycpWrapper.css({"border-right":"none", left:screenWidth});
					jQueryscreen.css("left", 0);
				}
				jQueryrotator.css({width:screenWidth + jQuerycpWrapper.outerWidth()});
			}
			
			//init buttons
			var initButtons = function() {
				jQueryplayBtn 	= jQuerybuttonPanel.find("div.play-btn");
				var jQueryprevBtn = jQuerybuttonPanel.find("div.prev-btn");
				var jQuerynextBtn = jQuerybuttonPanel.find("div.next-btn");
			
				//config directional buttons
				if (displayDBtns) {
					jQueryprevBtn.click(prevImg);
					jQuerynextBtn.click(nextImg);
				}
				else {
					jQueryprevBtn.hide();
					jQuerynextBtn.hide();
				}
				
				//config play button
				if (displayPlayBtn) {
					if (rotate) {
						jQueryplayBtn.addClass("pause");
					}			
					jQueryplayBtn.click(togglePlay);
				}
				else {
					jQueryplayBtn.hide();
				}
				
				if (pauseMouseover) {
					jQueryrotator.hover(pause, play);
				}
				
				if (displaySideBtns) {
					jQueryscreen.append("<div class='s-prev'></div><div class='s-next'></div>");
					jQuerysPrev = jQueryscreen.find(".s-prev");
					jQuerysNext = jQueryscreen.find(".s-next");
					jQuerysPrev.click(prevImg).hover(buttonOver,buttonOut).mousedown(preventDefault);
					jQuerysNext.click(nextImg).hover(buttonOver,buttonOut).mousedown(preventDefault);
					if (cpMouseover) {
						jQuerysPrev.css("left",-jQuerysPrev.width());
						jQuerysNext.css("margin-left",0);
						jQueryrotator.hover(showSideButtons, hideSideButtons);
					}
				}
				
				var jQuerybuttons = jQuerybuttonPanel.find(">div").css({width:buttonWidth, height:buttonHeight}).mouseover(buttonOver).mouseout(buttonOut).mousedown(preventDefault);
				if (cpVertical) {
					jQueryprevBtn.addClass("up");
					jQuerynextBtn.addClass("down");
					jQuerybuttons.css("margin-bottom", margin);									   
					jQuerybuttonPanel.width(jQuerybuttons.outerWidth());
					if (MSIE7_BELOW) {
						jQuerybuttonPanel.height(jQuerybuttonPanel.find(">div:visible").size() * jQuerybuttons.outerHeight(true));
					}
					if (displayThumbs && thumbWidth > buttonWidth) {
						var m = thumbWidth - buttonWidth;
						switch (ALIGN[cpAlign]) {
							case ALIGN["RT"]: case ALIGN["RC"]: case ALIGN["RB"]:
								jQuerybuttonPanel.css("margin-left", m);
								break;
							default:
								jQuerybuttonPanel.css("margin-right", m);
						}
					}
				}
				else {
					jQuerybuttons.css("margin-right", margin);
					jQuerybuttonPanel.height(jQuerybuttons.outerHeight());
					if (MSIE7_BELOW) {
						jQuerybuttonPanel.width(jQuerybuttonPanel.find(">div:visible").size() * jQuerybuttons.outerWidth(true));
					}
					if (displayThumbs && thumbHeight > buttonHeight) {
						var m = thumbHeight - buttonHeight;
						switch (ALIGN[cpAlign]) {
							case ALIGN["TL"]: case ALIGN["TC"]: case ALIGN["TR"]:
								jQuerybuttonPanel.css("margin-bottom", m);
								break;
							default:
								jQuerybuttonPanel.css("margin-top", m);
						}
					}
				}
			}			
			
			//init timer bar
			var initTimerBar = function() {
				jQuerytimer = jQueryscreen.find("#timer").data("pct", 1);
				if (displayTimer) {
					var align = opts.timer_align.toLowerCase();
					jQuerytimer.css("visibility", "visible");
					jQuerytimer.css("top", align == "top" ? 0 : screenHeight - jQuerytimer.height());
				}
				else {
					jQuerytimer.hide();
				}
			}
			
			//init items
			var initItems = function() {
				var padding = jQueryinnerText.outerHeight() - jQueryinnerText.height();
				jQuerythumbs.each(
					function(n) {
						var jQueryimgLink = jQuery(this).find(">a:first");
						var itemEffect = EFFECTS[jQuery(this).attr("effect")];
						if (itemEffect == undefined || itemEffect ==  EFFECTS["h.slide"] || itemEffect ==  EFFECTS["v.slide"]) {
							itemEffect = EFFECTS[globalEffect];
						}
						else {
							checkEffect(itemEffect);
						}
						jQuery(this).data({imgurl:jQueryimgLink.attr("href"), caption:jQueryimgLink.attr("title"), effect:itemEffect, delay:getPosNumber(jQuery(this).attr("delay"), globalDelay)});
						
						initTextData(jQuery(this), padding);
						jQueryitems[n] = jQuery(this);
						
						if (displayNumbers) {
							jQuery(this).append(n+1);
						}
					}
				);
				jQueryinnerText.css({width:"auto", height:"auto"}).html("");
				jQuerytextBox.css("visibility", "visible");
				
				if (opts.shuffle) {
					shuffleItems(displayThumbs && displayThumbImg);
				}
				
				if (displayThumbs) {
					if (displayThumbImg) {
						jQuerythumbs.addClass("image");
						jQuerythumbs.find(">a").removeAttr("title");
						var jQuerythumbImg = jQuerythumbs.find(">a>img");
						jQuerythumbImg.removeAttr("alt");
						jQuerythumbImg.each(function() {
							if (jQuery(this)[0].complete || jQuery(this)[0].readyState == "complete") {
								jQuery(this).css({top:(thumbHeight - jQuery(this).height())/2,left:(thumbWidth - jQuery(this).width())/2});
							}
							else {
								jQuery(this).load(function() {
									jQuery(this).css({top:(thumbHeight - jQuery(this).height())/2,left:(thumbWidth - jQuery(this).width())/2});
								});
							}
						});
					}
				
					jQuerythumbs.css({width:thumbWidth, height:thumbHeight, "line-height":thumbHeight + "px"}).mouseover(itemOver).mouseout(itemOut).mousedown(preventDefault);
					jQuerythumbPanel.click(selectItem);
					if (cpVertical) {
						jQuerythumbs.css("margin-bottom", margin);
						jQuerylist.width(jQuerythumbs.outerWidth());
						jQuerythumbPanel.width(jQuerylist.width());
						if (MSIE7_BELOW) {
							jQuerythumbPanel.height(numItems * jQuerythumbs.outerHeight(true));
						}
						//check uneven size
						if ((displayDBtns || displayPlayBtn) && (buttonWidth > thumbWidth)) {
							var m = buttonWidth - thumbWidth;
							switch (ALIGN[cpAlign]) {
								case ALIGN["RT"]: case ALIGN["RC"]: case ALIGN["RB"]:
									jQuerythumbPanel.css("margin-left", m);
									break;
								default:
									jQuerythumbPanel.css("margin-right", m);
							}
						}
						
						//check overflow
						var maxHeight = screenHeight - (jQuerybuttonPanel.height() + margin);
						if (jQuerythumbPanel.height() > maxHeight) {
							var unitSize = jQuerythumbs.outerHeight(true);
							jQuerylist.addClass("inside").height(numItems * unitSize);
							jQuerythumbPanel.css({height:Math.floor(maxHeight/unitSize) * unitSize - margin, "margin-bottom":margin});
							var range = jQuerythumbPanel.height() - (jQuerylist.height() - margin);
							
							jQuerythumbPanel.append("<div class='back-scroll'></div><div class='fwd-scroll'></div>");
							var jQuerybackScroll = jQuerythumbPanel.find(".back-scroll");
							var jQueryfwdScroll = jQuerythumbPanel.find(".fwd-scroll");
							jQuerybackScroll.css({height:unitSize, width:"100%"});
							jQueryfwdScroll.css({height:unitSize, width:"100%", top:"100%", "margin-top":-unitSize});
							jQuerybackScroll.hover(
									function() {
										jQueryfwdScroll.show();
										var speed = -jQuerylist.stop(true).position().top * SCROLL_RATE;
										jQuerylist.stop(true).animate({top:0}, speed, "linear", function() { jQuerybackScroll.hide(); });	  
									},
									stopList);
							
							jQueryfwdScroll.hover(
									function() {
										jQuerybackScroll.show();
										var speed = (-range + jQuerylist.stop(true).position().top) * SCROLL_RATE;
										jQuerylist.stop(true).animate({top:range}, speed, "linear", function() { jQueryfwdScroll.hide(); });	  		  
									},
									stopList);
							
							jQueryrotator.bind(UPDATE_LIST, function() {
								if(!jQuerylist.is(":animated")) {								
									var pos = jQuerylist.position().top + (currIndex * unitSize);
									if (pos < 0 || pos > jQuerythumbPanel.height() - jQuerythumbs.outerHeight()) {
										pos = -currIndex * unitSize;
										if (pos < range) {
											pos = range;
										}
										jQuerylist.stop(true).animate({top:pos}, ANIMATE_SPEED, 
																		function() { 
																			jQuery(this).position().top == 0 ? jQuerybackScroll.hide() : jQuerybackScroll.show();
																			jQuery(this).position().top == range ? jQueryfwdScroll.hide() : jQueryfwdScroll.show();																		
																		});
									}
								}
							});
						}
					}
					else {
						jQuerythumbs.css("margin-right", margin);
						jQuerylist.height(jQuerythumbs.outerHeight());
						jQuerythumbPanel.height(jQuerylist.height());
						if (MSIE7_BELOW) {
							jQuerythumbPanel.width(numItems * jQuerythumbs.outerWidth(true));
						}
						//check uneven size
						if ((displayDBtns || displayPlayBtn) && buttonHeight > thumbHeight) {
							var m = buttonHeight - thumbHeight;
							switch (ALIGN[cpAlign]) {
								case ALIGN["TL"]: case ALIGN["TC"]: case ALIGN["TR"]:
									jQuerythumbPanel.css("margin-bottom", m);
									break;
								default:
									jQuerythumbPanel.css("margin-top", m);
							}
						}
						
						//check overflow
						var maxWidth =  screenWidth - (jQuerybuttonPanel.width() + margin);
						if (jQuerythumbPanel.width() > maxWidth) {							
							var unitSize = jQuerythumbs.outerWidth(true);
							jQuerylist.addClass("inside").width(numItems * unitSize);
							jQuerythumbPanel.css({width:Math.floor(maxWidth/unitSize) * unitSize - margin, "margin-right":margin});
							var range = jQuerythumbPanel.width() - (jQuerylist.width() - margin);
							
							jQuerythumbPanel.append("<div class='back-scroll'></div><div class='fwd-scroll'></div>");
							var jQuerybackScroll = jQuerythumbPanel.find(".back-scroll");
							var jQueryfwdScroll = jQuerythumbPanel.find(".fwd-scroll");
							jQuerybackScroll.css({width:unitSize, height:"100%"});
							jQueryfwdScroll.css({width:unitSize, height:"100%", left:"100%", "margin-left":-unitSize});
							
							jQuerybackScroll.hover(
									function() {
										jQueryfwdScroll.show();
										var speed = -jQuerylist.stop(true).position().left * SCROLL_RATE;
										jQuerylist.stop(true).animate({left:0}, speed, "linear", function() { jQuerybackScroll.hide(); });	  
									},
									stopList);
							
							jQueryfwdScroll.hover(
									function() {
										jQuerybackScroll.show();
										var speed = (-range + jQuerylist.stop(true).position().left) * SCROLL_RATE;
										jQuerylist.stop(true).animate({left:range}, speed, "linear", function() { jQueryfwdScroll.hide(); });	  		  
									},
									stopList);
							
							jQueryrotator.bind(UPDATE_LIST, function() {
								if(!jQuerylist.is(":animated")) {								
									var pos = jQuerylist.position().left + (currIndex * unitSize);
									if (pos < 0 || pos > jQuerythumbPanel.width() - jQuerythumbs.outerWidth()) {
										pos = -currIndex * unitSize;
										if (pos < range) {
											pos = range;
										}
										jQuerylist.stop(true).animate({left:pos}, ANIMATE_SPEED, 
																		function() { 
																			jQuery(this).position().left == 0 ? jQuerybackScroll.hide() : jQuerybackScroll.show();
																			jQuery(this).position().left == range ? jQueryfwdScroll.hide() : jQueryfwdScroll.show();																		
																		});
									}
								}
							});
						}
					}
					
					initTooltip();
				}
				else {
					jQuerythumbs.hide();
				}
			}			
			
			//init text data
			var initTextData = function(jQueryitem, padding) {				
				var jQueryp = jQueryitem.find(">div:hidden");
				var textWidth =  getPosNumber(parseInt(jQueryp.css("width")) - padding, 300);
				var textHeight = getPosNumber(parseInt(jQueryp.css("height")) - padding, 0);
				jQueryinnerText.width(textWidth).html(jQueryp.html());
				if (textHeight < jQueryinnerText.height()) {
					textHeight = jQueryinnerText.height();
				}
				jQueryitem.data("textbox", {x:jQueryp.css("left"), y:jQueryp.css("top"), w:textWidth + padding, h:textHeight + padding + 1, color:jQueryp.css("color"), bgcolor:jQueryp.css("background-color")});
			}
			
			//init tool tip
			var initTooltip = function() {
				if (tipType == "text") {
					jQuery("body").append("<div id='rotator-tooltip'><div class='tt-txt'></div></div>");
					jQuerytooltip = jQuery("body").find("#rotator-tooltip");
					jQuerythumbs.mouseover(showTooltip).mouseout(hideTooltip).mousemove(moveTooltip);
					switch (ALIGN[cpAlign]) {
						case ALIGN["TL"]: case ALIGN["TC"]: case ALIGN["TR"]:
							jQuerytooltip.data("bottom",true).addClass("txt-down");
							break;
						default:
							jQuerytooltip.data("bottom",false).addClass("txt-up");
					}
				}
				else if (tipType == "image") {
					var content = "<div id='rotator-tooltip'>";
					for (var i = 0; i < numItems; i++) {	
						var jQueryimg = jQueryitems[i].find(">a:first>img");
						if (jQueryimg.size() == 1) {
							content += "<img src='" + jQueryimg.attr("src") + "' />";
						}
						else {
							content += "<img/>";
						}
					}
					content += "</div>";
					jQuery("body").append(content);
					jQuerytooltip = jQuery("body").find("#rotator-tooltip");
					switch (ALIGN[cpAlign]) {
						case ALIGN["TL"]: case ALIGN["TC"]: case ALIGN["TR"]:
							jQuerythumbs.mouseover(showHImgTooltip);
							jQuerytooltip.data("bottom",true).addClass("img-down");
							break;
						case ALIGN["LT"]: case ALIGN["LC"]: case ALIGN["LB"]:
							jQuerythumbs.mouseover(showVImgTooltip);
							jQuerytooltip.data("right",true).addClass("img-right");
							break;	
						case ALIGN["RT"]: case ALIGN["RC"]: case ALIGN["RB"]:
							jQuerythumbs.mouseover(showVImgTooltip);
							jQuerytooltip.data("right",false).addClass("img-left");
							break;	
						default:
							jQuerythumbs.mouseover(showHImgTooltip);
							jQuerytooltip.data("bottom",false).addClass("img-up");
					}
					jQuerythumbs.mouseout(hideTooltip);
				}
				
				if (jQuery.browser.msie && parseInt(jQuery.browser.version) <= 6) {
					try {
						jQuerytooltip.css("background-image", "none").children().css("margin",0);
					}
					catch (ex) {
					}
				}
			}
			
			//show image tooltip
			var showHImgTooltip = function(e) {
				var jQueryimg = jQuerytooltip.find(">img").eq(jQuery(this).index());
				if (jQueryimg.attr("src")) {
					jQuerytooltip.find(">img").hide();
					jQueryimg.show();
					if (jQueryimg[0].complete || jQueryimg[0].readyState == "complete") {	
						var yOffset = jQuerytooltip.data("bottom") ? jQuery(this).outerHeight() : -jQuerytooltip.outerHeight();
						var offset = jQuery(this).offset();
						jQuerytooltip.css({top:offset.top + yOffset, left:offset.left + ((jQuery(this).outerWidth() - jQuerytooltip.outerWidth())/2)})
								.stop(true, true).delay(TOOLTIP_DELAY).fadeIn(300);
					}
				}
			}
			
			//show image tooltip
			var showVImgTooltip = function(e) {
				var jQueryimg = jQuerytooltip.find(">img").eq(jQuery(this).index());
				if (jQueryimg.attr("src")) {
					jQuerytooltip.find(">img").hide();
					jQueryimg.show();
					if (jQueryimg[0].complete || jQueryimg[0].readyState == "complete") {
						var xOffset = jQuerytooltip.data("right") ? jQuery(this).outerWidth() : -jQuerytooltip.outerWidth();
						var offset = jQuery(this).offset();
						jQuerytooltip.css({top:offset.top + ((jQuery(this).outerHeight() - jQuerytooltip.outerHeight())/2), left:offset.left + xOffset})
								.stop(true, true).delay(TOOLTIP_DELAY).fadeIn(300);
					}
				}
			}
			
			//show tooltip
			var showTooltip = function(e) {
				var caption = jQueryitems[jQuery(this).index()].data("caption");
				if (caption != "") {					
					jQuerytooltip.find(">div.tt-txt").html(caption);
					var yOffset = jQuerytooltip.data("bottom") ? 0 : -jQuerytooltip.outerHeight(true);
					jQuerytooltip.css({top:e.pageY + yOffset, left:e.pageX}).stop(true, true).delay(TOOLTIP_DELAY).fadeIn(300);
				}
			}
			
			//tooltip move
			var moveTooltip = function(e) {
				var yOffset = jQuerytooltip.data("bottom") ? 0 : -jQuerytooltip.outerHeight(true);
				jQuerytooltip.css({top:e.pageY + yOffset, left:e.pageX});
			}
			
			//hide tooltip
			var hideTooltip = function() {
				jQuerytooltip.stop(true, true).fadeOut(0);
			}
			
			//display control panel
			var displayCPanel = function() {
				if (!cpVertical) {
					jQuerycpanel.stop(true).animate({top:jQuerycpanel.data("pos"), opacity:1}, ANIMATE_SPEED);
				}
				else {
					jQuerycpanel.stop(true).animate({left:jQuerycpanel.data("pos"), opacity:1}, ANIMATE_SPEED);
				}
			}
			
			//hide control panel
			var hideCPanel = function() {
				if (!cpVertical) {
					jQuerycpanel.stop(true).animate({top:jQuerycpanel.data("offset"), opacity:0}, ANIMATE_SPEED);
				}
				else {
					jQuerycpanel.stop(true).animate({left:jQuerycpanel.data("offset"), opacity:0}, ANIMATE_SPEED);
				}
			}
			
			var showSideButtons = function() {
				jQuerysPrev.stop(true).animate({left:0}, ANIMATE_SPEED);
				jQuerysNext.stop(true).animate({"margin-left":-jQuerysNext.width()}, ANIMATE_SPEED);
			}
			
			var hideSideButtons = function() {
				jQuerysPrev.stop(true).animate({left:-jQuerysPrev.width()}, ANIMATE_SPEED);
				jQuerysNext.stop(true).animate({"margin-left":0}, ANIMATE_SPEED);
			}
			
			//select list item
			var selectItem = function(e) {
				var jQueryitem = jQuery(e.target);
				if (jQueryitem[0].nodeName != "LI") {
					jQueryitem = jQueryitem.parents("li").eq(0);
				}
				var i = jQueryitem.index();
				if (i > -1 && i != currIndex) {	
					dir = i < currIndex ? PREV : NEXT; 
					resetTimer();
					prevIndex = currIndex;
					currIndex = i;
					loadContent(currIndex);
					hideTooltip();
				}
				return false;
			}
			
			//on item mouseover
			var itemOver = function() {
				jQuery(this).addClass("thumb-over");
			}
			
			//on item mouseout
			var itemOut = function() {
				jQuery(this).removeClass("thumb-over");
			}
			
			//go to previous image
			var prevImg = function() {
				dir = PREV;
				resetTimer();
				prevIndex = currIndex;
				currIndex = (currIndex > 0) ? (currIndex - 1) : (numItems - 1);
				loadContent(currIndex);
				return false;
			}
			
			//go to next image
			var nextImg = function() {
				dir = NEXT;
				resetTimer();
				prevIndex = currIndex;
				currIndex = (currIndex < numItems - 1) ? (currIndex + 1) : 0;
				loadContent(currIndex);
				return false;
			}
			
			//play/pause
			var togglePlay = function() {
				rotate = !rotate;
				jQuery(this).toggleClass("pause", rotate);
				rotate ? startTimer() : pauseTimer();
				return false;
			}
			
			//play
			var play = function() {
				rotate = true;
				jQueryplayBtn.toggleClass("pause", rotate);
				startTimer();
			}

			//pause
			var pause = function() {
				rotate = false;
				jQueryplayBtn.toggleClass("pause", rotate);
				pauseTimer();
			}
			
			//pause on last item
			var pauseLast = function(i) {
				if (i == numItems - 1) {
					rotate = false;
					jQueryplayBtn.toggleClass("pause", rotate);
				}
			}
					
			//on button over
			var buttonOver = function() {
				jQuery(this).addClass("button-over");
			}
			
			//on button out
			var buttonOut = function() {
				jQuery(this).removeClass("button-over");
			}
			
			//update text box
			var updateText = function(e) {
				if (!jQuerytextBox.data("visible")) {
					jQuerytextBox.data("visible", true);
					var text = jQueryitems[currIndex].find(">div:first").html();
					if (text && text.length > 0) {			
						var data = jQueryitems[currIndex].data("textbox");
						jQueryinnerText.css("color",data.color);
						jQuerytextBox.find(".inner-bg").css({"background-color":data.bgcolor, height:data.h-1});
						switch(TEXT_EFFECTS[textEffect]) {
							case TEXT_EFFECTS["fade"]:
								fadeInText(text, data);
								break;
							case TEXT_EFFECTS["down"]:
								expandText(text, data, {width:data.w, height:0}, {height:data.h});
								break;
							case TEXT_EFFECTS["right"]:
								expandText(text, data, {width:0, height:data.h}, {width:data.w});
								break;
							case TEXT_EFFECTS["left"]:
								expandText(text, data, {"margin-left":data.w, width:0, height:data.h}, {width:data.w, "margin-left":0});
								break;
							case TEXT_EFFECTS["up"]:
								expandText(text, data, {"margin-top":data.h, height:0, width:data.w}, {height:data.h, "margin-top":0});
								break;
							default:
								showText(text, data);
						}
					}					
				}
			}
			
			//reset text box
			var resetText = function() {
				jQuerytextBox.data("visible", false).stop(true, true);
				switch(TEXT_EFFECTS[textEffect]) {
					case TEXT_EFFECTS["fade"]:
					case TEXT_EFFECTS["down"]:
					case TEXT_EFFECTS["right"]:
					case TEXT_EFFECTS["left"]:
					case TEXT_EFFECTS["up"]:
						if (jQuery.browser.msie) {
							jQueryinnerText.css("opacity",0);
						}
						jQuerytextBox.fadeOut(ANIMATE_SPEED, function() { jQuery(this).css("display", "none"); });
						break;
					default:
						jQuerytextBox.css("display", "none");
				}
			}
			
			//expand text effect
			var expandText = function(text, data, props1, props2) {
				jQueryinnerText.css("opacity",1).html("");
				jQuerytextBox.stop(true, true).css({display:"block", top:data.y, left:data.x, "margin-top":0, "margin-left":0}).css(props1).animate(props2, ANIMATE_SPEED, 
					function () {  
						jQueryinnerText.html(text);
					});  
			}
			
			//fade in text effect
			var fadeInText = function(text, data) {
				jQueryinnerText.css("opacity",1).html(text);
				jQuerytextBox.css({top:data.y, left:data.x, width:data.w, height:data.h})
						.stop(true, true).fadeIn(ANIMATE_SPEED, function() {
																	if (jQuery.browser.msie) {
																		jQueryinnerText[0].style.removeAttribute('filter'); 
																	}});  
			}
			
			//show text effect
			var showText = function(text, data) {
				jQuerytextBox.stop(true).css({display:"block", top:data.y, left:data.x, width:data.w, height:data.h});  
				jQueryinnerText.html(text);
			}
			
			//display text panel on mouseover
			var displayText = function() {
				jQueryrotator.unbind(UPDATE_TEXT).bind(UPDATE_TEXT, updateText).trigger(UPDATE_TEXT);
			}

			//hide text panel on mouseovers
			var hideText = function() {
				jQueryrotator.unbind(UPDATE_TEXT);
				resetText();
			}
			
			//load current content
			var loadContent = function(i) {
				jQueryrotator.trigger(UPDATE_LIST);
				if (playOnce) {
					pauseLast(i);
				}
				
				//select thumb
				jQuerythumbs.filter(".curr-thumb").removeClass("curr-thumb");
				jQuerythumbs.eq(i).addClass("curr-thumb");
				
				//set delay
				delay = jQueryitems[i].data("delay");
				
				//reset text
				resetText();
				if (!textSync) {
					jQueryrotator.trigger(UPDATE_TEXT);
				}
				
				//set link
				if (jQuerymainLink) {
					var jQuerycurrLink = jQueryitems[i].find(">a:nth-child(2)");
					var href = jQuerycurrLink.attr("href");
					if (href) {
						jQuerymainLink.unbind("click", preventDefault).css("cursor", "pointer").attr({href:href, target:jQuerycurrLink.attr("target")});
					}
					else {
						jQuerymainLink.click(preventDefault).css("cursor", "default");
					}
				}
				
				//load image
				if (jQueryitems[i].data("img")) {
					jQuerypreloader.hide();
					displayContent(jQueryitems[i].data("img"));
				}	
				else {	
					//load new image
					var jQueryimg = jQuery("<img class='main-img'/>");
					jQueryimg.load(
						function() {
							jQuerypreloader.hide();
							storeImg(jQueryitems[i], jQuery(this));
							displayContent(jQuery(this));
						}
					).error(
						function() {
							alert("Error loading image");
						}
					);
					jQuerypreloader.show();					
					jQueryimg.attr("src", jQueryitems[i].data("imgurl"));
				}	    
			}
			
			//display content
			var displayContent = function(jQueryimg) {
				//clear
				if (vStripeEffect) {
					vStripes.clear();
					setPrevious();
				}
				if (hStripeEffect) {
					hStripes.clear();
					setPrevious();
				}
				if (blockEffect) {
					blocks.clear();
					setPrevious();
				}
				
				//get effect number
				var effect = jQueryitems[currIndex].data("effect");
				if (effect == EFFECTS["none"] || effect == undefined) {
					showContent(jQueryimg);
					return;
				}
				else if (effect == EFFECTS["fade"]) {
					fadeInContent(jQueryimg);
					return;
				}
				else if (effect == EFFECTS["h.slide"]) {
					slideContent(jQueryimg, "left", screenWidth);
					return;
				}
				else if (effect == EFFECTS["v.slide"]) {
					slideContent(jQueryimg, "top", screenHeight);
					return;
				}
				
				if (effect == EFFECTS["random"]) {
					effect = Math.floor(Math.random() * (ei - 5));
				}
				
				if (effect <= EFFECTS["spiral.out"]) {
					blocks.displayContent(jQueryimg, effect);
				}
				else if (effect <= EFFECTS["vert.random.fade"]) {
					vStripes.displayContent(jQueryimg, effect);
				}
				else {
					hStripes.displayContent(jQueryimg, effect);
				}
			}
			
			//set previous
			var setPrevious = function() {
				if (prevIndex >= 0) {
					var currSrc = jQuerymainLink.find("img#curr-img").attr("src");
					var prevSrc = jQueryitems[prevIndex].data("imgurl");
					if (currSrc != prevSrc) {
						jQuerymainLink.find("img.main-img").attr("id","").hide();
						var jQueryimg = jQuerymainLink.find("img.main-img").filter(function() { return jQuery(this).attr("src") == prevSrc; });
						jQueryimg.eq(0).show();
					}
				}
			}
			
			//display content (no effect)
			var showContent = function(jQueryimg) {
				if (textSync) {
					jQueryrotator.trigger(UPDATE_TEXT);
				}
				jQuerymainLink.find("img.main-img").attr("id","").hide();
				jQueryimg.attr("id", "curr-img").show();
				startTimer();
			}
			
			//display content (fade effect)
			var fadeInContent = function(jQueryimg) {
				jQuerymainLink.find("img#curr-img").stop(true, true);
				jQuerymainLink.find("img.main-img").attr("id","").css("z-index", 0);
				jQueryimg.attr("id", "curr-img").stop(true, true).css({opacity:0,"z-index":1}).show().animate({opacity:1}, duration, easing,
					function() {
						jQuerymainLink.find("img.main-img:not('#curr-img')").hide();
						if (textSync) {
							jQueryrotator.trigger(UPDATE_TEXT);
						}
						startTimer();
					}
				);
			}
			
			//slide content
			var slideContent = function(jQuerycurrImg, pos, moveby) {
				jQuerystrip.stop(true,true);
				var jQueryprevImg = jQuery("#curr-img", jQuerystrip);
				if (jQueryprevImg.size() > 0) {
					jQuerystrip.find(".main-img").attr("id","").parents(".content-box").css({top:0,left:0});
					jQuerycurrImg.attr("id", "curr-img").parents(".content-box").show();
					var jQueryimg, dest;
					if (dir == PREV) {
						jQuerystrip.css(pos, -moveby);
						jQueryimg = jQueryprevImg;
						dest = 0;
					}
					else {
						jQueryimg = jQuerycurrImg;
						dest = -moveby;
					}
					jQueryimg.parents(".content-box").css(pos,moveby);
					var prop = (pos == "top") ? {top:dest} : {left:dest};
					jQuerystrip.stop(true,true).animate(prop, duration, easing,
										function() {
											jQuerystrip.find(".main-img:not('#curr-img')").parents(".content-box").hide();
											jQuerystrip.find("#curr-img").parents(".content-box").show();
											jQueryimg.parents(".content-box").css({top:0,left:0});
											jQuerystrip.css({top:0,left:0});
											if (textSync) {
												jQueryrotator.trigger(UPDATE_TEXT);
											}
											startTimer();
										});
				}
				else {
					jQuerystrip.css({top:0,left:0});
					jQuerystrip.find(".main-img").parents(".content-box").hide().css({top:0,left:0});
					jQuerycurrImg.attr("id", "curr-img").parents(".content-box").show();
					if (textSync) {
						jQueryrotator.trigger(UPDATE_TEXT);
					}
					startTimer();
				}
			}
			
			//load image
			var loadImg = function(loadIndex) {
				try {
					var jQueryitem = jQueryitems[loadIndex];
					var jQueryimg = jQuery("<img class='main-img'/>");					
					jQueryimg.load(function() {
								if (!jQueryitem.data("img")) {
									storeImg(jQueryitem, jQuery(this));
								}
								loadIndex++
								if (loadIndex < numItems) {
									loadImg(loadIndex);
								}
							})
						.error(function() {
								//error loading image, continue next
								loadIndex++
								if (loadIndex < numItems) {
									loadImg(loadIndex);
								}
							});
					jQueryimg.attr("src", jQueryitem.data("imgurl"));	
				}
				catch(ex) {}
			}
			
			//process & store image
			var storeImg = function(jQueryitem, jQueryimg) {
				if (globalEffect == "h.slide" || globalEffect == "v.slide") {
					jQuerystrip.append(jQueryimg);
					centerImg(jQueryimg);
					var jQuerydiv = jQuery("<div class='content-box'></div>").css({width:screenWidth, height:screenHeight});
					jQueryimg.wrap(jQuerydiv);
					jQueryimg.css("display","block");
					var jQuerylink = jQueryitem.find(">a:nth-child(2)");
					if (jQuerylink) {
						jQueryimg.wrap(jQuerylink);
					}
				}
				else {
					jQuerymainLink.append(jQueryimg);
					centerImg(jQueryimg);
				}
				jQueryitem.data("img", jQueryimg);
			}
			
			//center image
			var centerImg = function(jQueryimg) {
				if (autoCenter && jQueryimg.width() > 0 && jQueryimg.height() > 0) {
					var tDiff = (screenHeight - jQueryimg.height())/2;
					var lDiff = (screenWidth  - jQueryimg.width())/2
					var top = 0, left = 0, vPad = 0, hPad = 0;
					if (tDiff > 0) {
						vPad = tDiff;
					}
					else if (tDiff < 0) {
						top = tDiff;
					}				
					if (lDiff > 0) {
						hPad = lDiff;
					}
					else if (lDiff < 0) {
						left = lDiff;
					}
					jQueryimg.css({top:top, left:left, "padding-top":vPad, "padding-bottom":vPad, "padding-left":hPad, "padding-right":hPad});
				}
			}
			
			//start timer
			var startTimer = function() {
				if (rotate && timerId == null) {
					var duration = Math.round(jQuerytimer.data("pct") * delay);
					jQuerytimer.animate({width:(screenWidth+1)}, duration, "linear");
					timerId = setTimeout(nextImg, duration);
				}
			}
			
			//reset timer
			var resetTimer = function() {
				clearTimeout(timerId);
				timerId = null;
				jQuerytimer.stop(true).width(0).data("pct", 1);
			}
			
			//pause timer
			var pauseTimer = function() {
				clearTimeout(timerId);
				timerId = null;
				var pct = 1 - (jQuerytimer.width()/(screenWidth+1));
				jQuerytimer.stop(true).data("pct", pct);
			}
			
			var stopList = function() {
				jQuerylist.stop(true);
			}
			
			//shuffle items
			var shuffleItems = function() {			
				for (var i = 0; i < jQueryitems.length; i++) {
					var ri = Math.floor(Math.random() * jQueryitems.length);
					var temp = jQueryitems[i];
					jQueryitems[i] = jQueryitems[ri];
					jQueryitems[ri] = temp;
				}
			}
			
			//shuffle items
			var shuffleItems = function(deepReplace) {
				if (deepReplace) {
					for (var i = 0; i < numItems; i++) {
						jQueryitems[i] = jQuerythumbs.eq(i).clone(true);
					}
				}
				
				for (var i = 0; i < numItems; i++) {
					var ri = Math.floor(Math.random() * numItems);
					var temp = jQueryitems[i];
					jQueryitems[i] = jQueryitems[ri];
					jQueryitems[ri] = temp;
				}
				
				if (deepReplace) {
					for (var i = 0; i < numItems; i++) {
						jQuerythumbs.eq(i).replaceWith(jQueryitems[i]);
					}				
					jQuerythumbs = jQuerylist.find(">li");
				}
			}
			
			//check effect
			var checkEffect = function(num) {
				if (num == EFFECTS["random"]) {
					blockEffect = hStripeEffect = vStripeEffect = true;
				}
				else if (num <= EFFECTS["spiral.out"]) {
					blockEffect = true;
				}
				else if (num <= EFFECTS["vert.random.fade"]) {
					vStripeEffect = true;
				}
				else if (num <= EFFECTS["horz.random.fade"]) {
					hStripeEffect = true;
				}
			}
			
			//prevent default behavior
			var preventDefault = function() {
				return false;
			}
		}		
			
		//get positive number
		var getPosNumber = function(val, defaultVal) {
			if (!isNaN(val) && val > 0) {
				return val;
			}
			return defaultVal;
		}

		//get nonnegative number
		var getNonNegNumber = function(val, defaultVal) {
			if (!isNaN(val) && val >= 0) {
				return val;
			}
			return defaultVal;
		}
		
		//shuffle array
		var shuffleArray = function(arr) {
			var total =  arr.length;
			for (var i = 0; i < total; i++) {
				var ri = Math.floor(Math.random() * total);
				var temp = arr[i];
				arr[i] = arr[ri];
				arr[ri] = temp;
			}	
		}
		
		var defaults = { 
			width:825,
			height:300,			
			thumb_width:24,
			thumb_height:24,
			button_width:24,
			button_height:24,
			button_margin:4,			
			auto_start:true,
			delay:DEFAULT_DELAY,
			transition:"fade",
			transition_speed:DURATION,
			cpanel_position:INSIDE,
			cpanel_align:"BR",
			timer_align:"top",
			display_thumbs:true,
			display_side_buttons:false,
			display_dbuttons:true,
			display_playbutton:true,
			display_imgtooltip:true,
			display_numbers:true,
			display_thumbimg:false,
			display_timer:true,
			mouseover_pause:false,
			cpanel_mouseover:false,
			text_mouseover:false,
			text_effect:"fade",
			text_sync:true,
			tooltip_type:"text",
			shuffle:false,
			play_once:false,
			auto_center:false,
			block_size:BLOCK_SIZE,
			vert_size:STRIPE_SIZE,
			horz_size:STRIPE_SIZE,
			block_delay:25,
			vstripe_delay:75,
			hstripe_delay:75,
			easing:""
		};
		
		var opts = jQuery.extend({}, defaults, params);
		return this.each(
			function() {
				var rotator = new Rotator(jQuery(this), opts);
				rotator.init();
			}
		);
	}
})(jQuery);