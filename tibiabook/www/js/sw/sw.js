'use strict';

self.addEventListener('push', function(event){

	var json = event.data.json();

	var title = 'NotificaÃ§Ã£o';
	var body = 'DevNux';
	var icon = '/icon.png';
	var tag = false;

	if(json.title && json.title !== ''){
		title = json.title
	}
	if(json.body && json.body !== ''){
		body = json.body
	}
	if(json.icon && json.icon !== ''){
		icon = json.icon
	}
	if(json.tag && json.tag !== ''){
		tag = json.tag
	}

	event.waitUntil(
		self.registration.showNotification(title, {
			'body': body,
			'icon': icon,
			'tag': tag
		})
	);
});

self.addEventListener('notificationclick', function(event) {

	event.notification.close();

	// This looks to see if the current is already open and
	// focuses if it is
	event.waitUntil(clients.matchAll({
		type: "window"
	}).then(function(clientList) {
		for (var i = 0; i < clientList.length; i++) {
		  var client = clientList[i];
		  if (client.url == '/' && 'focus' in client)
			return client.focus();
		}
	if (clients.openWindow)
	  return clients.openWindow(event.notification.tag);
	}));
});