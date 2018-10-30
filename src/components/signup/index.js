import React, {Component} from 'react';
import Form from '../form';
import Notice from '../notice';

class SignUp extends Component {

	constructor(props) {
		super(props);

		this.state = {
			completed: false,
			notice: {
				heading: '',
				message: '',
				success: null,
			},
		}

		this.displayNotice = this.displayNotice.bind(this);
	}

	// When the user has submitted a form we want to display a notice
	// to let them know if the subscription was a success
	displayNotice(isError) {
		this.setState(() => ({notice: isError ? this.props.notice.error : this.props.notice.success}) );
		this.props.toggleComplete();
		if (! isError ) {
			this.props.isSuccess();
		}
	}

	getNotice() {
		const {heading, message, success} = this.state.notice;

		return (
			<Notice 
			heading={heading} 
			message={message} 
			onClose={() => {this.props.onClose()}}
			onBack={() => {this.props.toggleComplete()}} 
			success={success} />
		);
	}

	render() {
		return (
			<div className="relative-newsletter-modal__signup">
				{this.props.isComplete ? this.getNotice() : <Form didSubmit={this.displayNotice} campaignId={this.props.campaignId}/>}
			</div>
		);
	}
}

export default SignUp;