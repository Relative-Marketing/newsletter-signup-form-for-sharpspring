import React, {Component} from 'react';

class Copy extends Component {
	render() {
		return (
			<div className="relative-newsletter__copy">
				{this.props.children}
			</div>
		);
	}
}

export default Copy;