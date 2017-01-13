;(function($) {
	"use strict";
	var pluginName = 'GeminiUploader';

	function Upload(id, options, keyList) {
		this.id = id;
		this.rootHandel = '#'+id;
		this.options = $.extend({}, $.fn[pluginName].defaults, options);
		this.keyList = keyList;
		this.BDUploader = undefined;
		this.allowNum = this.options.fileNumLimit; //剩余上传的个数
		this.fileFinshedNum = 0;
		var ratio = window.devicePixelRatio || 1;
		this.thumbnailWidth = 150*ratio;
		this.thumbnailHeight = 150*ratio;
		var wrapWidth = $(this.rootHandel).width();
		this.liHeight = parseInt((wrapWidth-30)/100*31);//TODO:RESIZE 
		this.wrapHeight = (this.liHeight * (Math.ceil(keyList.length / 3)))+40;

	}
	
	
	//插件扩展方法
	$.extend(Upload.prototype, {
		debug: false,
		dump: function(object) {
			if (this.debug) {
				if (typeof object == 'object') {
					console.dir(object);
				} else {
					console.log(object);
				}
			}
		},
		initBaseWrap: function(keyList,picList) {//html基本结构初始化
			var wrapHtmlStr = '<div class="gemini-uploader">'+
									'<div class="queueList">';
			
			wrapHtmlStr += '<ul class="labelList labListItem_'+keyList.length+'">';
			for(var i=0; i<keyList.length; i++){
				wrapHtmlStr += '<li><span>'+keyList[i].form_label+'</span></li>';
			}
			wrapHtmlStr += '</ul>';			
			wrapHtmlStr += '<ul class="fileList fileListItem_'+keyList.length+'">';
			for(var i=0; i<picList.length; i++){
				wrapHtmlStr += '<li><p class="imgWrap"><img src='+picList[i].path+' data-id="'+ picList[i].id +'" /></p>'
				//wrapHtmlStr += '<div class="file-panel">' +
                //    '<span class="cancel">删除</span>' +
                //    '</div>';
				wrapHtmlStr += '<span class="success"></span></li>';
			}			
			wrapHtmlStr += '</ul></div>';
			wrapHtmlStr +='<div class="statusBar">';
			wrapHtmlStr +=	'<div class="progress">';
			wrapHtmlStr +=		'<span class="text">0%</span>';
			wrapHtmlStr +=		'<span class="percentage"></span>';
			wrapHtmlStr +=	'</div>';
			wrapHtmlStr +='</div>';
			
			wrapHtmlStr += '<div id="'+this.id+'_btns" class="btns"><div id="'+this.id+'_picker" class="uploadBtn">选择照片</div></div></div>';
			
			wrapHtmlStr += '</div>';
			var root = this.rootHandel;
			$(root).html(wrapHtmlStr);			
			$(".fileList >li").height(this.liHeight);
			$(root).find(".queueList").height(this.wrapHeight);
			
			
			//var list = $(root).find(".fileList")[0];
			//if(list!=null){
			//	Sortable.create(list); // 创建预览图片拖动
			//}

			var self = this;
			$("#"+this.id+'_picker').on('click', function() {
				if ( $(this).hasClass( 'disabled' ) ) {
					return false;
				}
				var status = $(this).attr("data-status");
				if(status == "ready"){
					$(this).addClass( 'disabled' ).find(".webuploader-pick").text("上传中...");
					$(self.rootHandel).find('.statusBar').show().end()
									.find('.file-panel').hide();
					self.BDUploader.upload();
					return false;
				}
			});
			
		},
		initUploader: function() {
			var self = this;
			self.BDUploader = WebUploader.create(self.options);
			
			//self.BDUploader.onFileQueued = function( file ) {};
			
			for (var funcName in self.options.uploadEvents) {
				if (typeof self.options.uploadEvents[funcName] == 'function') {
					switch (funcName) {
						case 'dndAccept': //阻止此事件可以拒绝某些类型的文件拖入进来。目前只有 chrome 提供这样的 API，且只能通过 mime-type 验证。
							self.BDUploader.on(funcName, function(items) {
								self.options.uploadEvents[funcName](items);
							});
							break;
						case 'filesQueued': //当一批文件添加进队列以后触发。
							self.BDUploader.on(funcName, function(files) {
								self.options.uploadEvents[funcName](files);
							});
							break;
						case 'reset': //当 uploader 被重置的时候触发。
						case 'startUpload': //当开始上传流程时触发。
						case 'stopUpload': //当开始上传流程暂停时触发。
						case 'uploadFinished': //当所有文件上传结束时触发。
							self.BDUploader.on(funcName, function() {
								self.options.uploadEvents[funcName]();
							});
							break;
						case 'uploadBeforeSend': //当某个文件的分块在发送前触发，主要用来询问是否要添加附带参数，大文件在开起分片上传的前提下此事件可能会触发多次。
							self.BDUploader.on(funcName, function(object, data, headers) {
								self.options.uploadEvents[funcName](object, data, headers);
							});
							break;
						case 'uploadAccept': //当某个文件上传到服务端响应后，会派送此事件来询问服务端响应是否有效。如果此事件handler返回值为false, 则此文件将派送server类型的uploadError事件。
							self.BDUploader.on(funcName, function(object, ret) {
								self.options.uploadEvents[funcName](object, ret);
							});
							break;
						case 'uploadProgress': //上传过程中触发，携带上传进度。
							self.BDUploader.on(funcName, function(file, percentage) {
								self.options.uploadEvents[funcName](file, percentage);
							});
							break;
						case 'uploadError': //当文件上传出错时触发。
							self.BDUploader.on(funcName, function(file, reason) {
								self.options.uploadEvents[funcName](file, reason);
							});
							break;
						case 'uploadSuccess': //当文件上传成功时触发。//用户自定义回调处理
							self.BDUploader.on(funcName, function(file, response) {
								self.options.uploadEvents[funcName](file, response);
							});
							break;
						case 'error': //当validate不通过时，会以派送错误事件的形式通知调用者。通过upload.on('error', handler)可以捕获到此类错误，目前有以下错误会在特定的情况下派送错来。
							self.BDUploader.on(funcName, function(type) {
								self.options.uploadEvents[funcName](type);
							});
							break;
						case 'beforeFileQueued': //当文件被加入队列之前触发，此事件的handler返回值为false，则此文件不会被添加进入队列。
						case 'fileQueued': //当一批文件添加进队列以后触发。
						case 'fileDequeued': //当文件被移除队列后触发。
						case 'uploadStart': //某个文件开始上传前触发，一个文件只会触发一次。
						case 'uploadComplete': //不管成功或者失败，文件上传完成时触发。
						default:
							self.BDUploader.on(funcName, function(file) {
								self.options.uploadEvents[funcName](file);
							});
							break;
					}
				}
			}

			/**
			 * 默认上传添加队列处理
			 */
			if (!self.options.uploadEvents.fileQueued) {
				self.BDUploader.on('fileQueued', function(file) {
					var btn = "#"+self.id+"_picker";
					if (self.allowNum == 0 ) {
						self.BDUploader.removeFile(file);
						ui_alert('上传文件数量超出限制,最多上传' + self.options.fileNumLimit + '个文件');
						return false;
					}
					if(self.allowNum == 1){
						$(btn).attr("data-status","ready");
						$(btn).find(".webuploader-pick").text('开始上传');
					}else{
						$(btn).find(".webuploader-pick").text('继续添加');
					}
					self.showThumb(file);
					self.allowNum--;
				});
			}
			/**
			 * 默认文件发送前处理
			 */
			if (!self.options.uploadEvents.uploadBeforeSend) {
				self.BDUploader.on('uploadBeforeSend', function(object, data, headers) {
					if(typeof(self.keyList) == "object"){
						var keyList = self.keyList;
						//var index = self.fileIndex;
						for(var i=0 ; i<keyList.length;i++){
							if(keyList[i].has != 1 ){
								data['form_key'] = keyList[i].form_key;
								data['form_label'] = keyList[i].form_label;
								keyList[i].has = 1;
								break;
							}
						}
					}
					//self.fileIndex++;
				});
			}

			
			/**
			 * 默认上传进度处理
			 */
			if (!self.options.uploadEvents.uploadProgress) {
				self.BDUploader.on('uploadProgress', function(file, percentage) {
					/*var $li = $('#' + file.id),
						$percent = $li.find('.progress .progress-bar');
					if (percentage > 0.2) {
						$percent.text('已上传' + parseInt(percentage * 100, 10) + '%');
					}
					$percent.css('width', percentage * 100 + '%');
					if (percentage == 1) {
						$percent.text('上传完成100%').removeClass('active').attr('aria-valuenow', parseInt(percentage * 100, 10));
					}*/
				});
			}
			/**
			 * 默认文件上传验证错误提示
			 */
			if (!self.options.uploadEvents.error) {
				self.BDUploader.on('error', function(type) {
					var title = '',
						msg = '',
						errtype = 'error';
					switch (type) {
						case 'Q_EXCEED_NUM_LIMIT':
							title = '上传文件数量超出限制';
							msg = '最多上传' + self.options.fileNumLimit + '个文件';
							break;
						case 'F_EXCEED_SIZE':
							title = '单个文件大小超出限制';
							msg = '最大上传' + WebUploader.formatSize(self.options.fileSingleSizeLimit);
							break;
						case 'Q_EXCEED_SIZE_LIMIT':
							title = '文件总大小超出限制';
							msg = '最大上传' + WebUploader.formatSize(self.options.fileSizeLimit);
							break;
						case 'Q_TYPE_DENIED':
							title = '文件类型限制';
							msg = self.options.accept.extensions;
							break;
						case 'F_DUPLICATE':
							title = '同名文件已存在';
							break;
						default:
							title = '未知类型上传错误' + type;
							break;
					}
					ui_alert(title + msg);
				});
			}
			/**
			 * 默认文件上传出错时处理
			 */
			if (!self.options.uploadEvents.uploadError) {
				self.BDUploader.on('uploadError', function(file, reason) {
					self.dump(file);
					self.dump(reason);
				});
			}
			/**
			 * 默认文件上传成功处理
			 */
			if (!self.options.uploadEvents.uploadSuccess) {
				self.BDUploader.on('uploadSuccess', function(file, response) {

					//客户端完成上传，服务端返回错误信息
					if (response.status == 0) {
						$('#' + file.id).remove();
						self.BDUploader.removeFile(file.id, true);
						ui_alert(response.info);
						self.dump('上传完成但服务端有错误');
						return false;
					}else{
						var $li = $('#'+file.id);
						$li.find('img').attr('data-id',response.info.id);	
					}
					self.dump('上传成功');

				});
			}
			/**
			 * 默认文件上传完成时触发。不管成功或者失败
			 */
			if (!self.options.uploadEvents.uploadComplete) {
				self.BDUploader.on('uploadComplete', function(file) {
					self.dump('上传完成');
					self.fileFinshedNum++;
					
					
					var percent = Math.ceil((self.fileFinshedNum/self.options.fileNumLimit)*100);
					$(self.rootHandel).find('.text').text(percent+"%");
					$(self.rootHandel).find('.percentage').width(percent+"%");
					
					if(self.fileFinshedNum == self.options.fileNumLimit){
						var btn = "#"+self.id+"_picker";
						$(btn).hide();
						$(self.rootHandel).find('.statusBar').hide();
						//$(btn).find(".webuploader-pick").text('上传完成');
					}
				});
			}

		},



		getHiddenValue: function() {
			return $("#" + this.options.hiddenName).val();
		},

		isSupportBase64: ( function() {
                var data = new Image();
                var support = true;
                data.onload = data.onerror = function() {
                    if( this.width != 1 || this.height != 1 ) {
                        support = false;
                    }
                }
                data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
                return support;
        } )(),
		supportTransition: (function(){
                var s = document.createElement('p').style,
                    r = 'transition' in s ||
                            'WebkitTransition' in s ||
                            'MozTransition' in s ||
                            'msTransition' in s ||
                            'OTransition' in s;
                s = null;
                return r;
        })(),
		showThumb: function(file){//创建图片预览
			var self = this;
            var $li = $( '<li id="' + file.id + '">' +
                    //'<p class="title">' + file.name + '</p>' +
                    '<p class="imgWrap"></p>'+
                    '<p class="progress"><span></span></p>' +
                    '</li>' ),
                $btns = $('<div class="file-panel">' +
                    '<span class="cancel">删除</span>' +
                    '<span class="rotateRight">向右旋转</span>' +
                    '</div>').appendTo( $li ),
                $prgress = $li.find('p.progress span'),
                $wrap = $li.find( 'p.imgWrap' ),
                $info = $('<p class="error"></p>'),

                showError = function( code ) {
                    switch( code ) {
                        case 'exceed_size':
                            text = '文件大小超出';
                            break;

                        case 'interrupt':
                            text = '上传暂停';
                            break;

                        default:
                            text = '上传失败，请重试';
                            break;
                    }

                    $info.text( text ).appendTo( $li );
                };

            if ( file.getStatus() === 'invalid' ) {
                showError( file.statusText );
            } else {
                // @todo lazyload
                $wrap.text( '预览中' );
				self.BDUploader.makeThumb( file, function( error, src ) {
                    var img;
                    if ( error ) {
                        $wrap.text( '不能预览' );
                        return;
                    }
                    if( self.isSupportBase64 ) {
                        img = $('<img src="'+src+'">');
                        $wrap.empty().append( img );
                    } else {
						$wrap.text("不支持预览");
                    }
                }, self.thumbnailWidth, self.thumbnailHeight );

                //percentages[ file.id ] = [ file.size, 0 ];
                file.rotation = 0;
            }

            file.on('statuschange', function( cur, prev ) {
                if ( prev === 'progress' ) {
                    $prgress.hide().width(0);
                } else if ( prev === 'queued' ) {
                    $li.off( 'mouseenter mouseleave' );
                    $btns.remove();
                }

                // 成功
                if ( cur === 'error' || cur === 'invalid' ) {
                    showError( file.statusText );
                   // percentages[ file.id ][ 1 ] = 1;
                } else if ( cur === 'interrupt' ) {
                    showError( 'interrupt' );
                } else if ( cur === 'queued' ) {
                    $info.remove();
                    $prgress.css('display', 'block');
                   // percentages[ file.id ][ 1 ] = 0;
                } else if ( cur === 'progress' ) {
                    $info.remove();
                    $prgress.css('display', 'block');
                } else if ( cur === 'complete' ) {
                    $prgress.hide().width(0);
                    $li.append( '<span class="success"></span>' );
                }

                $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
            });

            $btns.on( 'click', 'span', function() {
                var index = $(this).index(),
                    deg;

                switch ( index ) {
                    case 0:
                        self.BDUploader.removeFile( file );
						var $li = $('#'+file.id);

						//delete// percentages[ file.id ];
						//updateTotalProgress();
						$li.off().find('.file-panel').off().end().remove();
						self.allowNum++;
						var btn = "#"+self.id+"_picker";
						$(btn).attr("data-status","0").find(".webuploader-pick").text('继续添加');
						
                        return;

                    case 1:
                        file.rotation += 90;
                        break;

                    case 2:
                        file.rotation -= 90;
                        break;
                }

                if ( self.supportTransition ) {
                    deg = 'rotate(' + file.rotation + 'deg)';
                    $wrap.css({
                        '-webkit-transform': deg,
                        '-mos-transform': deg,
                        '-o-transform': deg,
                        'transform': deg
                    });
                } else {
                    $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
                }


            });
			
			var root = this.rootHandel;
            $li.appendTo( $(root).find(".fileList") );

		}
	});
	//插件入口
	$.fn[pluginName] = function(keyList,picList,options) {
		var opts = $.extend({
			pick: '#' + this.attr("id")+'_picker',
		}, options);
		
		var id = this.attr("id");
		var picNums = (keyList.length - picList.length);
		if(picNums >= 0){
			opts.fileNumLimit = picNums;
		}
		//console.log(id);
		var uploader = new Upload(id, opts, keyList);
		uploader.initBaseWrap(keyList,picList);
		uploader.initUploader();
		
	}
		//插件默认参数
	$.fn[pluginName].defaults = {
		dnd: undefined, //指定Drag And Drop拖拽的容器，如果不指定，则不启动 
		disableGlobalDnd: false, //是否禁掉整个页面的拖拽功能，如果不禁用，图片拖进来的时候会默认被浏览器打开
		paste: undefined, 
		accept: null, //指定接受哪些类型的文件。 由于目前还有ext转mimeType表，所以这里需要分开指定。
		/**
		* 
		* title {String} 文字描述
		* extensions {String} 允许的文件后缀，不带点，多个用逗号分割。
		* mimeTypes {String} 多个用逗号分割。
		* 如：{
		title: 'Images',
		extensions: 'gif,jpg,jpeg,bmp,png',
		mimeTypes: 'image/*'
		}
		*/

		thumb: {}, //配置生成缩略图的选项
		compress:{
                    width: 1024,
                    quality: 90,
                    allowMagnify: false,
                    crop: false,
                    preserveHeaders: true,
                    noCompressIfLarger: false,
                    compressSize: 0
                },
		auto: false, //设置为 true 后，不需要手动调用上传，有文件选择即开始上传。
		runtimeOrder: 'html5,flash', //指定运行时启动顺序。默认会想尝试 html5 是否支持，如果支持则使用 html5, 否则则使用 flash.可以将此值设置成 flash，来强制使用 flash 运行时。
		prepareNextFile: true, //是否允许在文件传输时提前把下一个文件准备好。 对于一个文件的准备工作比较耗时，比如图片压缩，md5序列化。 如果能提前在当前文件传输期处理，可以节省总体耗时。
		chunked: false, //是否要分片处理大文件上传。
		chunkSize: 5242880, //如果要分片，分多大一片？ 默认大小为5M.
		chunkRetry: 2, //如果某个分片由于网络问题出错，允许自动重传多少次？
		threads: 3, //上传并发数。允许同时最大上传进程数。
		formData: {}, //文件上传请求的参数表，每次发送都会发送此对象中的参数。
		fileVal: 'file', //设置文件上传域的name。
		method: 'POST', //文件上传方式，POST或者GET。
		sendAsBinary: false, //是否已二进制的流的方式发送文件，这样整个上传内容php://input都为文件内容， 其他参数在$_GET数组中。
		fileNumLimit: undefined, //验证文件总数量, 超出则不允许加入队列。
		fileSizeLimit: 20*1024*1024, //验证文件总大小是否超出限制, 超出则不允许加入队列。以字节为单位
		fileSingleSizeLimit: undefined, //验证单个文件大小是否超出限制, 超出则不允许加入队列。以字节为单位
		duplicate: undefined, //去重， 根据文件名字、文件大小和最后修改时间来生成hash Key.
		disableWidgets: undefined, //默认所有 Uploader.register 了的 widget 都会被加载，如果禁用某一部分，请通过此 option 指定黑名单。 
		swf: 'static/js/webuploader/Uploader.swf',
		server: '/mobile/files/upload', // 文件接收服务端。
		/**
		 * ΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔ
		 * 以上参数是百度上传组件默认参数
		 * 以下参数是非百度上传组件参数（即本插件根据实际开发设置的参数）
		 * ∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨
		 */
		uploadEvents: {}, //上传事件
		delUrl: '/mobile/files/delete',
		separator: ',', //默认逗号
	}

})(jQuery);