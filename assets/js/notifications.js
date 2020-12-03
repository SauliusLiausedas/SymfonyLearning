import $ from 'jquery';
import axios from 'axios';

$(function() {
    fetchNotificationCount()
})

function fetchNotificationCount() {
    const url = '/notification/unread-count'
    axios.get(url).then((res) => $('#notification-count').text(res))
        .catch(err => {
            console.log(err)
        })
}