var LadderTable = React.createClass({
  getInitialState: function() {
    return {
      games: []
    };
  },

  componentDidMount: function() {
    this.serverRequest = $.get(this.props.source, function (result) {
      	var games = result.data;
  		this.setState({
    	    games: games
		});

    }.bind(this));

  },

  componentWillUnmount: function() {
    this.serverRequest.abort();
  },

  // render: function() {
  // 	var results = this.state.games;
  // 	console.log(results);
  //   return (
  //     <ol>
  //       {results.map(function(result) {
  //         return <li key={result.id}>{result.player1.name} {result.score1} – {result.score2} {result.player2.name}</li>;
  //       })}
  //     </ol>
  //   );
  // }
  render: function() {
  	var results = this.state.games;
  	if( results.length > 0 ) {
	    return (
	    	<table>
		    	<thead>
		    		<tr>
			    		<th>
			    			Rubrik
			    		</th>
			    	</tr>
		    	</thead>
		    	<tbody>
			      	{results.map(function(result) {
			          	return <tr data-id={result.id}>
				          	<td data-id="{result.id}">
				          		Spelare 1: {result.player1.name}
				          		<br></br>
				          		Spelare 2: {result.player2.name}
			          		</td>
			          	</tr>;
			        })}
		        </tbody>
		     </table>
	    );
	  	console.log('success');
	} else {
		return <table><tbody><tr data-id="hej"><td>hej</td></tr></tbody></table>;
	}
  }
});

ReactDOM.render(
  <LadderTable source="http://api.angryladder.dev/v1/games" />,
  document.getElementById('table-ladder')
);