import $ from 'jquery';
import axios from 'axios';

$(function() {
    fetchNotificationCount()
})

function fetchNotificationCount() {
    const url = '/notification/unread-count'
    axios.get(url).then((res) => {
        const { count } = res.data;
        $('#notification-count').text(count)
        setTimeout(() => {
            fetchNotificationCount()
        }, 5000)
    })
        .catch(err => {
            console.log(err)
        })
}