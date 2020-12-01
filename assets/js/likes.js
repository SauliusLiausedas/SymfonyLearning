import $ from 'jquery';
import axios from 'axios';

$(function () {
    $('.likingBtn').click(function() {
        const like = $(this).data('like')
        const id = $(this).data('id')
        const action = like ? 'like' : 'unlike'
        axios.get(`/likes/${action}/${id}`).then((data) => {
            if (data.data.count || data.data.count === 0) {
                $(this).hide()
                const newBtn = $(this).siblings('button')
                $(newBtn).find('span').text(data.data.count)
                $(newBtn).show()
            }
        })
            .catch(err => console.log(err))
    })
})