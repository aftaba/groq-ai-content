import { __ } from '@wordpress/i18n';

import { useState } from 'react';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { Panel, PanelBody, TextareaControl, CheckboxControl, Button } from '@wordpress/components';


import './editor.scss';

export default function Edit( {attributes, setAttributes} ) {
	const { blockText } = attributes;

	const [ aIcommand, setAICommand] = useState("Generate summary report of content in 3 lines");
	const [ apiError, setAPIError] = useState(false);
	const [ usePostDataContent, setUsePostDataContent] = useState(true);
	const [ isLoading, setIsLoading] = useState(false);
	
	const generateAIContent = () => {

		setIsLoading(true);
		setAPIError(false);

		fetch( groq_rest_data.rest_api_url , { 
      		method: "POST", 
			body: JSON.stringify({
				_nonce : groq_rest_data._nonce,
				ai_message : aIcommand,
				use_post_content : usePostDataContent,
				post_content :  usePostDataContent ? document.getElementsByClassName("is-root-container")[0].innerHTML.replace(/(<([^>]+)>)/ig, '') : ''
			}),
			headers: { 
				"Content-type": "application/json; charset=UTF-8",
			} 
		})
		.then(response => response.json()) 
		.then(result => {
			setIsLoading(false);
			if( result.success == true ) {
				setAttributes({
					'blockText' : result.data
				});
			} else {
				setAPIError(result.data)
			}
			
		}).catch(error => {
			setIsLoading(false);
			console.log(error);
		}); 
		
	}


	return [
        <InspectorControls>
            <Panel>
                <PanelBody
                    title={ __( 'Groq AI Settings', 'groq-ai-content' ) }
                    icon="admin-plugins"
                >
                    <TextareaControl
                        label={ __( 'Groq AI Playground', 'groq-ai-content' ) }
						help={ apiError ? <span style={{ color: 'red' }}>{apiError}</span>: ''}
                        value={ aIcommand }
						errorText={"Enter value"}
						onChange={ (value) => setAICommand(value) }/>
					
					<CheckboxControl
						label="Use Post Content Data For Report?"
						checked={usePostDataContent}
						onChange={ (value) => setUsePostDataContent(value)}
					/>

					<Button 
						disabled={isLoading}
						variant="primary" 
						onClick={generateAIContent} > 
							{isLoading ? "Generating.." : "Generate" }
					</Button>
                </PanelBody>
            </Panel>
        </InspectorControls>,
        <p { ...useBlockProps() }>
            <RichText
                className="block__text"
                keepPlaceholderOnFocus
                onChange={ ( blockText ) => setAttributes( { blockText } ) }
                placeholder={ blockText }
                tagName="span"
                value={ blockText }
            />
        </p>
    ];
}
