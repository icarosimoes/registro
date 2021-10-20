import React from 'react';
import ReactDOM from 'react-dom';

function Example() {
    return (
       <h1>Example Component</h1>
    );
}

export default Example;

if (document.getElementById('app')) {
    ReactDOM.render(<Example />, document.getElementById('app'));
}
