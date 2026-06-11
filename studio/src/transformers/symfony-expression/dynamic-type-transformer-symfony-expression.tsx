import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { SymfonyExpressionTransformerForm } from './symfony-expression-transformer-form'

@injectable()
export class DynamicTypeTransformerSymfonyExpression extends DynamicTypeTransformerAbstract {
    readonly id = 'symfonyExpression'
    readonly label = 'Symfony Expression'
    readonly group = 'dataManipulation' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <SymfonyExpressionTransformerForm onChange={ onChange } settings={ settings } />
    }
}
