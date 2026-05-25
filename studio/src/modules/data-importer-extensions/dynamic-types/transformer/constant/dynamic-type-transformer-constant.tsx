import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { ConstantTransformerForm } from './constant-transformer-form'

@injectable()
export class DynamicTypeTransformerConstant extends DynamicTypeTransformerAbstract {
    readonly id = 'constant'
    readonly label = 'Constant'
    readonly group = 'dataManipulation' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <ConstantTransformerForm onChange={ onChange } settings={ settings } />
    }
}
