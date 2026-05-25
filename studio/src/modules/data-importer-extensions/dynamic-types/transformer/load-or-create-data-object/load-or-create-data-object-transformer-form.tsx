import React from 'react'
import { Checkbox, Form, Input } from '@pimcore/studio-ui-bundle/components'
import { TransformerSettingsLayout } from '../transformer-settings-layout'

// Overrides core loadDataObject transformer. Adds createPath, publishOnCreate,
// and createIfNotFound settings on top of the core load-by-id behaviour.
// TODO: createPath should use a Pimcore object folder tree picker.

interface LoadOrCreateDataObjectTransformerConfig {
    createPath?: string
    publishOnCreate?: boolean
    createIfNotFound?: boolean
}

interface LoadOrCreateDataObjectTransformerFormProps {
    settings: LoadOrCreateDataObjectTransformerConfig
    onChange: (settings: LoadOrCreateDataObjectTransformerConfig) => void
}

export const LoadOrCreateDataObjectTransformerForm = ({ settings, onChange }: LoadOrCreateDataObjectTransformerFormProps): React.JSX.Element => {
    const update = (key: string, value: any): void => { onChange({ ...settings, [key]: value }) }

    return (
        <TransformerSettingsLayout>
            <Form.Item label="Create Path">
                <Input
                    onChange={ (e) => { update('createPath', e.target.value) } }
                    placeholder="/"
                    value={ settings.createPath ?? '/' }
                />
            </Form.Item>
            <Form.Item label="Publish on Create">
                <Checkbox
                    checked={ settings.publishOnCreate ?? false }
                    onChange={ (e) => { update('publishOnCreate', e.target.checked) } }
                />
            </Form.Item>
            <Form.Item label="Create if Not Found">
                <Checkbox
                    checked={ settings.createIfNotFound ?? false }
                    onChange={ (e) => { update('createIfNotFound', e.target.checked) } }
                />
            </Form.Item>
        </TransformerSettingsLayout>
    )
}
