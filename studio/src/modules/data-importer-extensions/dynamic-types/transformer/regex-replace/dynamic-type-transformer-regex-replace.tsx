import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { RegexReplaceTransformerForm } from './regex-replace-transformer-form'

@injectable()
export class DynamicTypeTransformerRegexReplace extends DynamicTypeTransformerAbstract {
    readonly id = 'regexReplace'
    readonly label = 'Regex Replace'
    readonly group = 'dataManipulation' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <RegexReplaceTransformerForm onChange={ onChange } settings={ settings } />
    }
}
