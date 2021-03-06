(function(c,r){
	var Nav = function(title,url,child){
		this.title = title;
		this.url = url;
		this.child = child || false;
	}
	var Header = function(){
		this.currentPath = r.path;
		this.nav = ko.observableArray([
				new Nav('仪表盘',c.path+'admin/index')
				,new Nav('用户',c.path+'admin/operate')
				,new Nav('设置',c.path+'admin/options/config')
		]);

		this.user = ko.observableArray([
				new Nav(c.username,'javascript:;',[
					new Nav('个人资料11',c.path+'admin/profile?do=edit')
					,new Nav('数据备份',c.path+'admin/console/backup')
				])
				,new Nav('网站前台',c.path)
				,new Nav('退出登录',c.path+'admin/logout')
		]);
	}

	ko.applyBindings(new Header(),document.getElementById('header'));
})(_CONFIG_,new UrlRequest);