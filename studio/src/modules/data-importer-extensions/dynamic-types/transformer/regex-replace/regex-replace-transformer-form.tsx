import React from 'react'
import { Form, Input } from '@pimcore/studio-ui-bundle/components'
import { TransformerSettingsLayout } from '../transformer-settings-layout'

interface RegexReplaceTransformerConfig {
    search?: string
    replace?: string
}

interface RegexReplaceTransformerFormProps {
    settings: RegexReplaceTransformerConfig
    onChange: (settings: RegexReplaceTransformerConfig) => void
}

export const RegexReplaceTransformerForm = ({ settings, onChange }: RegexReplaceTransformerFormProps): React.JSX.Element => {
    const update = (key: string, value: string): void => { onChange({ ...settings, [key]: value }) }

    return (
        <TransformerSettingsLayout>
            <Form.Item label="Pattern">
                <Input
                    onChange={ (e) => { update('search', e.target.value) } }
                    value={ settings.search ?? '' }
                />
            </Form.Item>
            <Form.Item label="Replace">
                <Input
                    onChange={ (e) => { update('replace', e.target.value) } }
                    value={ settings.replace ?? '' }
                />
            </Form.Item>
        </TransformerSettingsLayout>
    )
}
