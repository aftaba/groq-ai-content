import { useBlockProps } from '@wordpress/block-editor';

export default function save({attributes}) {

	const { blockText } = attributes;
	console.log(attributes);
	return (

		<p { ...useBlockProps.save() }>
            { blockText }
        </p>
	);
}
