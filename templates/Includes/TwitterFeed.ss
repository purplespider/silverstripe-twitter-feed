<% cached TwitterCacheCounter %>

	<% if getLatestTweets %>
			<% loop getLatestTweets %>
			<p class="tweet">$text</p>
			<p class="twittime"><a href="http://twitter.com/$username/statuses/$id_str">$created_at.Ago</a> - <a href="http://twitter.com/$username">Follow Us</a></p>
			<p></p>
			<% end_loop %>
	<% else %>
		<p><a href="http://twitter.com/$get_username">Follow us on Twitter</a></p>
	<% end_if %>

<% end_cached %>